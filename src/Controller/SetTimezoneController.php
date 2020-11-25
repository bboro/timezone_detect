<?php

namespace Drupal\timezone_detect\Controller;

use Drupal\Component\Utility\Html;
use Drupal\Core\Access\CsrfTokenGenerator;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\MessageCommand;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\user\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTimezoneController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * @var \Symfony\Component\HttpFoundation\Request
   */
  private $request;

  /**
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  private $account;

  /**
   * @var \Psr\Log\LoggerInterface
   */
  private $logger;

  /**
   * @var \Drupal\Core\Access\CsrfTokenGenerator
   */
  private $tokenGenerator;

  /**
   * SetTimezoneController constructor.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param \Drupal\Core\Session\AccountProxyInterface $account
   * @param \Psr\Log\LoggerInterface $logger
   * @param \Drupal\Core\Access\CsrfTokenGenerator $tokenGenerator
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   */
  public function __construct(Request $request, AccountProxyInterface $account, LoggerInterface $logger, CsrfTokenGenerator $tokenGenerator, ConfigFactoryInterface $configFactory) {
    $this->request = $request;
    $this->account = $account;
    $this->logger = $logger;
    $this->tokenGenerator = $tokenGenerator;
    $this->configFactory = $configFactory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('request_stack')->getCurrentRequest(),
      $container->get('current_user'),
      $container->get('logger.factory')->get('timezone_detect'),
      $container->get('csrf_token'),
      $container->get('config.factory')
    );
  }

  /**
   * Update the users timezone.
   */
  public function updateTimezone() {
    $response = new AjaxResponse();
    // Unset session flag regardless of whether they are logged in or not to
    // avoid repeated attempts at this process that are likely to fail.
    unset($_SESSION['timezone_detect']['update_timezone']);
    // If they are logged in, set some data.
    if ($this->account->isAuthenticated()) {
      $config = $this->configFactory->get('timezone_detect.settings');
      // Check for $_POST data.
      // Timezone should be an IANA/Olson timezone id provided via $_POST.
      $timezone = Html::escape($this->request->request->get('timezone'));
      if (!isset($timezone)) {
        $this->logger->error('Attempting to set timezone for user @uid, but no timezone found in $_POST data; aborting.', ['@uid' => $this->account->id()]);
        return $response;
      }
      // Make sure we have a valid session token to prevent cross-site request
      // forgery.
      $token = $this->request->request->get('token');
      if (!isset($token) || !$this->tokenGenerator->validate($token)) {
        $this->logger->error('Attempting to set timezone for user @uid, but session token in $_POST data is empty or invalid; aborting.', ['@uid' => $this->account->id()]);
        return $response;
      }

      // Keep track of the last submitted timezone in case it's not valid so
      // that we don't keep POSTing it on every request.
      $_SESSION['timezone_detect']['current_timezone'] = $timezone;

      // Check valid timezone id.
      $zone_list = timezone_identifiers_list();
      if (!in_array($timezone, $zone_list)) {
        $this->logger->error('Attempting to set timezone for user @uid to @timezone, but that does not appear to be a valid timezone id; aborting.', ['@uid' => $this->account->id(), '@timezone' => $timezone]);
        return $response;
      }

      // Save timezone to account.
      User::load($this->account->id())
        ->set('timezone', $timezone)
        ->save();

      $message = $this->t('Your timezone has been set to @tz.', [
        '@tz' => $timezone,
      ]);
      $response->addCommand(new MessageCommand($message));

      if ($config->get('watchdog')) {
        $this->logger->notice('Set timezone for user @uid to @timezone.', ['@uid' => $this->account->id(), '@timezone' => $timezone]);
      }
    }

    return $response;
  }

  /**
   * Sets a cookie to ignore timezone detect for 3 months.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   The response.
   */
  public function setCookie() {
    $response = new Response('');
    // Unset session flag regardless of whether they are logged in or not to
    // avoid repeated attempts at this process that are likely to fail.
    unset($_SESSION['timezone_detect']['update_timezone']);
    // Make sure we have a valid session token to prevent cross-site request
    // forgery.
    $token = $this->request->request->get('token');
    if (!isset($token) || !$this->tokenGenerator->validate($token)) {
      $this->logger->error('Attempting to set timezone for user @uid, but session token in $_POST data is empty or invalid; aborting.', ['@uid' => $this->account->id()]);
      return $response;
    }
    if ($this->account->isAuthenticated()) {
      $domain = $this->request->getHost();
      $expire = strtotime('now + 3 months');
      $cookie = new Cookie('timezone_detect_ignore', TRUE, $expire, NULL, $domain, TRUE, FALSE);
      $response->headers->setCookie($cookie);
    }
    return $response;
  }

}
