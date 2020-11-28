<?php
use Dotenv\Repository\RepositoryBuilder;
use PhpOption\Option;

class JConfig {
	public $offline;
	public $offline_message;
	public $display_offline_message = 1;
	public $offline_image = '';
	public $sitename;
	public $editor = 'tinymce';
	public $captcha = '0';
	public $list_limit = 20;
	public $access = 1;
	public $debug = false;
	public $debug_lang = false;
	public $debug_lang_const = true;
	public $dbtype;
	public $host;
	public $user;
	public $password;
	public $db;
	public $dbprefix;
	public $dbencryption = 0;
	public $dbsslverifyservercert = false;
	public $dbsslkey = '';
	public $dbsslcert = '';
	public $dbsslca = '';
	public $dbsslcipher = '';
	public $force_ssl = 0;
	public $live_site = '';
	public $secret;
	public $gzip = false;
	public $error_reporting = 'default';
	public $helpurl = 'https://help.joomla.org/proxy?keyref=Help{major}{minor}:{keyref}&lang={langcode}';
	public $ftp_host = '';
	public $ftp_port = '';
	public $ftp_user = '';
	public $ftp_pass = '';
	public $ftp_root = '';
	public $ftp_enable = 0;
	public $offset = 'UTC';
	public $mailonline = true;
	public $mailer = 'mail';
	public $mailfrom;
	public $fromname;
	public $sendmail;
	public $smtpauth = false;
	public $smtpuser = '';
	public $smtppass = '';
	public $smtphost = 'localhost';
	public $smtpsecure = 'none';
	public $smtpport = 25;
	public $caching = 0;
	public $cache_handler = 'file';
	public $cachetime = 15;
	public $cache_platformprefix = false;
	public $MetaDesc = '';
	public $MetaTitle = true;
	public $MetaAuthor = true;
	public $MetaVersion = false;
	public $robots = '';
	public $sef = true;
	public $sef_rewrite = false;
	public $sef_suffix = false;
	public $unicodeslugs = false;
	public $feed_limit = 10;
	public $feed_email = 'none';
	public $log_path;
	public $tmp_path;
	public $lifetime = 15;
	public $session_handler = 'database';
	public $shared_session = false;
	public $session_metadata = true;

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

		$this->sitename = static::get('SITENAME', $defaultSiteName);

		$this->dbtype = static::get('DATABASE_TYPE');
		$this->host = static::get('DATABASE_HOST');
		$this->user = static::get('DATABASE_USER');
		$this->password = static::get('DATABASE_PASSWORD');
		$this->db = static::get('DATABASE_DB');
		$this->dbprefix = static::get('DATABASE_PREFIX');

		$this->log_path = static::get('LOG_PATH', JPATH_ADMINISTRATOR . '/logs');
		$this->tmp_path = static::get('TMP_PATH', JPATH_ROOT . '/tmp');

		$this->mailfrom = static::get('MAIL_SENDER', '/usr/sbin/sendmail');
		$this->mailfrom = static::get('MAIL_FROM');
		$this->fromname = static::get('MAIL_FROM_NAME', static::get('SITENAME', $defaultSiteName));

		// The default secret in installation/configuration.dist.php
		$this->secret = static::get('SITE_SECRET', 'FBVtggIk5lAzEU9H');
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
