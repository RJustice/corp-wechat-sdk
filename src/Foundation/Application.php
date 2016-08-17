<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * Application.php.
 *
 * Part of Overtrue\WeChat.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    overtrue <i@overtrue.me>
 * @copyright 2015
 *
 * @link      https://github.com/overtrue/wechat
 * @link      http://overtrue.me
 */
namespace RCorpWechat\Foundation;

use Doctrine\Common\Cache\Cache as CacheInterface;
use Doctrine\Common\Cache\FilesystemCache;
use RCorpWechat\Core\AccessToken;
use RCorpWechat\Core\Http;
use RCorpWechat\Support\Log;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimple\Container;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Application.
 *
 * @property \RCorpWechat\Server\Guard                    $server
 * @property \RCorpWechat\User\User                       $user
 * @property \RCorpWechat\User\Tag                        $user_tag
 * @property \RCorpWechat\User\Group                      $user_group
 * @property \RCorpWechat\Js\Js                           $js
 * @property \Overtrue\Socialite\SocialiteManager        $oauth
 * @property \RCorpWechat\Menu\Menu                       $menu
 * @property \RCorpWechat\Notice\Notice                   $notice
 * @property \RCorpWechat\Material\Material               $material
 * @property \RCorpWechat\Material\Temporary              $material_temporary
 * @property \RCorpWechat\Staff\Staff                     $staff
 * @property \RCorpWechat\Url\Url                         $url
 * @property \RCorpWechat\QRCode\QRCode                   $qrcode
 * @property \RCorpWechat\Semantic\Semantic               $semantic
 * @property \RCorpWechat\Stats\Stats                     $stats
 * @property \RCorpWechat\Payment\Merchant                $merchant
 * @property \RCorpWechat\Payment\Payment                 $payment
 * @property \RCorpWechat\Payment\LuckyMoney\LuckyMoney   $lucky_money
 * @property \RCorpWechat\Payment\MerchantPay\MerchantPay $merchant_pay
 * @property \RCorpWechat\Reply\Reply                     $reply
 * @property \RCorpWechat\Broadcast\Broadcast             $broadcast
 * @property \RCorpWechat\Card\Card                       $card
 */
class Application extends Container
{
    /**
     * Service Providers.
     *
     * @var array
     */
    protected $providers = [
        ServiceProviders\ServerServiceProvider::class,
        ServiceProviders\UserServiceProvider::class,
        ServiceProviders\JsServiceProvider::class,
        ServiceProviders\OAuthServiceProvider::class,
        ServiceProviders\MenuServiceProvider::class,
        ServiceProviders\NoticeServiceProvider::class,
        ServiceProviders\MaterialServiceProvider::class,
        ServiceProviders\StaffServiceProvider::class,
        ServiceProviders\UrlServiceProvider::class,
        ServiceProviders\QRCodeServiceProvider::class,
        ServiceProviders\SemanticServiceProvider::class,
        ServiceProviders\StatsServiceProvider::class,
        ServiceProviders\PaymentServiceProvider::class,
        ServiceProviders\POIServiceProvider::class,
        ServiceProviders\ReplyServiceProvider::class,
        ServiceProviders\BroadcastServiceProvider::class,
        ServiceProviders\CardServiceProvider::class,
    ];

    /**
     * Application constructor.
     *
     * @param array $config
     */
    public function __construct($config)
    {
        parent::__construct();

        $this['config'] = function () use ($config) {
            return new Config($config);
        };

        if ($this['config']['debug']) {
            error_reporting(E_ALL);
        }

        $this->registerProviders();
        $this->registerBase();
        $this->initializeLogger();

        Http::setDefaultOptions($this['config']->get('guzzle', ['timeout' => 5.0]));

        foreach (['app_id', 'secret'] as $key) {
            !isset($config[$key]) || $config[$key] = '***'.substr($config[$key], -5);
        }

        Log::debug('Current config:', $config);
    }

    /**
     * Add a provider.
     *
     * @param string $provider
     *
     * @return Application
     */
    public function addProvider($provider)
    {
        array_push($this->providers, $provider);

        return $this;
    }

    /**
     * Set providers.
     *
     * @param array $providers
     */
    public function setProviders(array $providers)
    {
        $this->providers = [];

        foreach ($providers as $provider) {
            $this->addProvider($provider);
        }
    }

    /**
     * Return all providers.
     *
     * @return array
     */
    public function getProviders()
    {
        return $this->providers;
    }

    /**
     * Magic get access.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function __get($id)
    {
        return $this->offsetGet($id);
    }

    /**
     * Magic set access.
     *
     * @param string $id
     * @param mixed  $value
     */
    public function __set($id, $value)
    {
        $this->offsetSet($id, $value);
    }

    /**
     * Register providers.
     */
    private function registerProviders()
    {
        foreach ($this->providers as $provider) {
            $this->register(new $provider());
        }
    }

    /**
     * Register basic providers.
     */
    private function registerBase()
    {
        $this['request'] = function () {
            return Request::createFromGlobals();
        };

        if (!empty($this['config']['cache']) && $this['config']['cache'] instanceof CacheInterface) {
            $this['cache'] = $this['config']['cache'];
        } else {
            $this['cache'] = function () {
                return new FilesystemCache(sys_get_temp_dir());
            };
        }

        $this['access_token'] = function () {
            return new AccessToken(
                $this['config']['app_id'],
                $this['config']['secret'],
                $this['cache']
            );
        };
    }

    /**
     * Initialize logger.
     */
    private function initializeLogger()
    {
        if (Log::hasLogger()) {
            return;
        }

        $logger = new Logger('rcorpwechat');

        if (!$this['config']['debug'] || defined('PHPUNIT_RUNNING')) {
            $logger->pushHandler(new NullHandler());
        } elseif ($logFile = $this['config']['log.file']) {
            $logger->pushHandler(new StreamHandler($logFile, $this['config']->get('log.level', Logger::WARNING)));
        }

        Log::setLogger($logger);
    }
}
