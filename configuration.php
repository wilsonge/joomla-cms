<?php
use Dotenv\Repository\RepositoryBuilder;
use PhpOption\Option;

class JConfig {
	public $offline;
	public $offline_message;
	public $display_offline_message;
	public $offline_image;
	public $sitename;
	public $editor;
	public $captcha;
	public $list_limit;
	public $access;
	public $debug;
	public $debug_lang;
	public $debug_lang_const;
	public $dbtype;
	public $host;
	public $user;
	public $password;
	public $db;
	public $dbprefix;
	public $dbencryption;
	public $dbsslverifyservercert;
	public $dbsslkey;
	public $dbsslcert;
	public $dbsslca;
	public $dbsslcipher;
	public $force_ssl;
	public $live_site;
	public $secret;
	public $gzip;
	public $error_reporting;
	public $helpurl;
	public $ftp_host;
	public $ftp_port;
	public $ftp_user;
	public $ftp_pass;
	public $ftp_root;
	public $ftp_enable;
	public $offset;
	public $mailonline;
	public $mailer;
	public $mailfrom;
	public $fromname;
	public $sendmail;
	public $smtpauth;
	public $smtpuser;
	public $smtppass;
	public $smtphost;
	public $smtpsecure;
	public $smtpport;
	public $caching;
	public $cache_handler;
	public $cachetime;
	public $cache_platformprefix;
	public $MetaDesc;
	public $MetaTitle;
	public $MetaAuthor;
	public $MetaVersion;
	public $robots;
	public $sef;
	public $sef_rewrite;
	public $sef_suffix;
	public $unicodeslugs;
	public $feed_limit;
	public $feed_email;
	public $log_path;
	public $tmp_path;
	public $lifetime;
	public $session_handler;
	public $shared_session;
	public $session_metadata;

	/**
	 * The environment repository instance.
	 *
	 * @var \Dotenv\Repository\RepositoryInterface|null
	 */
	protected static $repository;

	public function __construct()
	{
		$defaultSiteName = 'Joomla Site';
		$this->offline = static::get('SITE_OFFLINE', false);
		$this->offline_message = static::get('SITE_OFFLINE_MESSAGE', 'This site is down for maintenance.<br>Please check back again soon.');
		$this->display_offline_message = static::get('SITE_OFFLINE_MESSAGE_DISPLAYED', 1);
		$this->offline_message = static::get('SITE_OFFLINE_IMAGE', '');

		$this->sitename = static::get('SITE_NAME', $defaultSiteName);
		$this->MetaDesc = static::get('SITE_META_DESCRIPTION', '');
		$this->MetaTitle = static::get('SITE_META_TITLE', true);
		$this->MetaAuthor = static::get('SITE_META_AUTHOR', true);
		$this->MetaVersion = static::get('SITE_META_VERSION', false);
		$this->robots = static::get('SITE_ROBOTS', '');
		$this->live_site = static::get('SITE_URL_OVERRIDE', '');
		$this->force_ssl = static::get('SITE_SSL_ENFORCED', 0);
		$this->offset = static::get('SITE_TIMEZONE', 'UTC');

		$this->dbtype = static::get('DATABASE_TYPE');
		$this->host = static::get('DATABASE_HOST');
		$this->user = static::get('DATABASE_USER');
		$this->password = static::get('DATABASE_PASSWORD');
		$this->db = static::get('DATABASE_DB');
		$this->dbprefix = static::get('DATABASE_PREFIX');
		$this->dbencryption = static::get('DATABASE_ENCRYPTION', 0);
		$this->dbsslverifyservercert = static::get('DATABASE_SSL_VERIFY_CERT', false);
		$this->dbsslkey = static::get('DATABASE_SSL_KEY', '');
		$this->dbsslcert = static::get('DATABASE_SSL_CERT', '');
		$this->dbsslca = static::get('DATABASE_SSL_CA', '');
		$this->dbsslcipher = static::get('DATABASE_SSL_CIPHER', '');

		$this->caching = static::get('CACHING_STATUS', 0);
		$this->cache_handler = static::get('CACHING_HANDLER', 'file');
		$this->cache_time = static::get('CACHING_TIME', 15);
		$this->cache_platformprefix = static::get('CACHING_PREFIX', false);

		$this->log_path = static::get('LOG_PATH', JPATH_ADMINISTRATOR . '/logs');
		$this->tmp_path = static::get('TMP_PATH', JPATH_ROOT . '/tmp');

		$this->mailer = static::get('MAIL_SEND_MAIL', 'mail');
		$this->mailonline = static::get('MAIL_ONLINE', true);
		$this->mailfrom = static::get('MAIL_SENDER', '/usr/sbin/sendmail');
		$this->mailfrom = static::get('MAIL_FROM');
		$this->fromname = static::get('MAIL_FROM_NAME', static::get('SITENAME', $defaultSiteName));

		$this->sef = static::get('SEF_ENABLED', true);
		$this->sef_rewrite = static::get('SEF_REWRITE_ENABLED', false);
		$this->sef_suffix = static::get('SEF_SUFFIX_ENABLED', false);
		$this->unicodeslugs = static::get('SEF_UNICODE_SLUGS', false);
		$this->gzip = static::get('SEF_GZIP_ENABLED', false);

		// The default secret in installation/configuration.dist.php
		$this->secret = static::get('SITE_SECRET', 'FBVtggIk5lAzEU9H');

		$this->session_handler = static::get('SESSION_HANDLER', 'database');
		$this->shared_session = static::get('SESSION_SHARED', false);
		$this->session_metadata = static::get('SESSION_METADATA', true);
		$this->lifetime = static::get('SESSION_LIFETIME', 15);

		$this->editor = static::get('EDITOR_EXTENSION', 'tinymce');
		$this->captcha = static::get('CAPTCHA', 0);

		$this->access = static::get('ACCESS_LEVEL_DEFAULT', 1);
		$this->list_limit = static::get('LIST_DEFAULT_ITEMS', 20);
		$this->helpurl = static::get('JOOMLA_HELP_URL_OVERRIDE', 'https://help.joomla.org/proxy?keyref=Help{major}{minor}:{keyref}&lang={langcode}');

		$this->debug = static::get('APP_DEBUG', false);
		$this->debug_lang = static::get('APP_DEBUG_LANGUAGE', false);
		$this->debug_lang_const = static::get('APP_DEBUG_LANGUAGE_CONSTANT', true);
		$this->error_reporting = static::get('APP_DEBUG_PHP_ERROR_REPORTING', 'default');

		$this->feed_email = static::get('FEED_EMAIL', 'none');
		$this->feed_limit = static::get('FEED_LIMIT', 10);

		$this->smtphost = static::get('SMTP_HOST', 'localhost');
		$this->smtpsecure = static::get('SMTP_SECURE', 'none');
		$this->smtpport = static::get('SMTP_PORT', 25);
		$this->smtpauth = static::get('SMTP_AUTH', false);
		$this->smtpuser = static::get('SMTP_USERNAME', '');
		$this->smtppass = static::get('SMTP_PASSWORD', '');

		$this->ftp_enable = static::get('FTP_ENABLED', 0);
		$this->ftp_host = static::get('FTP_HOST', '');
		$this->ftp_port = static::get('FTP_PORT', '');
		$this->ftp_user = static::get('FTP_USERNAME', '');
		$this->ftp_pass = static::get('FTP_PASSWORD', '');
		$this->ftp_root = static::get('FTP_ROOT', '');
	}

	/**
	 * Gets the value of an environment variable.
	 *
	 * @param   string  $key      The environment variable name to load
	 * @param   mixed   $default  The default value to use
	 *
	 * @return mixed
	 */
	public static function get($key, $default = null)
	{
		if (static::$repository === null)
		{
			static::$repository = RepositoryBuilder::createWithDefaultAdapters()->immutable()->make();
		}

		return Option::fromValue(static::$repository->get($key))
			->map(function ($value) {
				switch (strtolower($value))
				{
					case 'true':
					case '(true)':
						return true;
					case 'false':
					case '(false)':
						return false;
					case 'null':
					case '(null)':
						return;
				}

				if (preg_match('/\A([\'"])(.*)\1\z/', $value, $matches))
				{
					return $matches[2];
				}

				return $value;
			})
			->getOrCall(function () use ($default) {
				return $default instanceof Closure ? $default() : $default;
			});
	}
}
