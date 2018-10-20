<?php
/**
 * Timber starter-theme
 * https://github.com/timber/starter-theme
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */

if ( ! class_exists( 'Timber' ) ) {
	add_action( 'admin_notices', function() {
		echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php') ) . '</a></p></div>';
	});

	add_filter('template_include', function($template) {
		return get_stylesheet_directory() . '/static/no-timber.html';
	});
	return;
}
/**
 * Sets the directories (inside your theme) to find .twig files
 */
Timber::$dirname = array('templates', 'views');

/**
 * By default, Timber does NOT autoescape values. Want to enable Twig's autoescape?
 * No prob! Just set this value to true
 */
Timber::$autoescape = false;

/**
 * We're going to configure our theme inside of a subclass of Timber\Site
 * You can move this to its own file and include here via php's include("MySite.php")
 */
class StarterSite extends TimberSite {

	private $manifest;
	/** Add timber support. */
	function __construct() {
		add_action( 'after_setup_theme', array( $this, 'theme_supports' ) );
		add_filter( 'timber_context', array( $this, 'add_to_context' ) );
		add_filter( 'get_twig', array( $this, 'add_to_twig' ) );
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ) );

		add_action( 'after_setup_theme', array( $this, 'get_manifest' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_static_assets' ) );
		parent::__construct();
	}

	/* This is where you can register custom post types */
	public function register_post_types() {
	}

	/* This is where you can register custom taxonomies */
	public function register_taxonomies() {
	}

	public function add_to_context( $context ) {
		$context['foo'] = 'bar';
		$context['stuff'] = 'I am a value set in your functions.php file';
		$context['notes'] = 'These values are available everytime you call Timber::get_context();';
		$context['menu'] = new TimberMenu();
		$context['test_menu'] = new TimberMenu('Testing Menu');
		$context['site'] = $this;
		return $context;
	}

	public function theme_supports() {
		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5', array(
				'comment-list',
				'comment-form',
				'search-form',
				'gallery',
				'caption'
			)
		);

		/*
		 * Enable support for Post Formats.
		 *
		 * See: https://codex.wordpress.org/Post_Formats
		 */
		add_theme_support(
			'post-formats', array(
				'aside',
				'image',
				'video',
				'quote',
				'link',
				'gallery',
				'audio',
			)
		);
		add_theme_support( 'menus' );
	}

	/**
	 * This would return 'foo bar!'.
	 *
	 * @param string $text being 'foo', then return 'foo bar!'.
	 * @return string
	 */
	public function myfoo( $text ) {
		$text .= ' bar!';
		return $text;
	}

	/**
	 * This is where you can add your own functions to twig
	 *
	 * @param string $twig geht extension
	 * @return object $twig
	 */
	public function add_to_twig( $twig ) {
		$twig->addExtension( new Twig_Extension_StringLoader() );
		$twig->addFilter('myfoo', new Twig_SimpleFilter('myfoo', array($this, 'myfoo')));
		return $twig;
	}

	public function get_manifest() {
		$manifestFile = get_theme_file_path().'/static/manifest.json';

		$this->manifest = file_exists($manifestFile) ?
			json_decode(file_get_contents($manifestFile), true) : [];
	}

	public function get_asset($name) {
		return isset($this->manifest[$name]) ? $this->manifest[$name] : $name;
	}

	public function load_static_assets() {
		$path = get_template_directory_uri().'/';

		wp_enqueue_style('styles', $path . $this->get_asset('main.css') );
		wp_enqueue_script('scripts', $path . $this->get_asset('main.js') );
	}

}

new StarterSite();
