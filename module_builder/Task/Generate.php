<?php

/**
 * @file
 * Definition of ModuleBuider\Task\Generate.
 */

namespace ModuleBuider\Task;

/**
 * Task handler for generating a component.
 *
 * (Replaces generate.inc.)
 */
class Generate extends Base {

  /**
   * The sanity level this task requires to operate.
   *
   * We have no sanity level; it's obtained from our base component generator.
   */
  protected $sanity_level = NULL;

  /**
   * Our base component type, i.e. either 'module' or 'theme'.
   */
  private $base;

  /**
   * Our base generator.
   */
  private $base_generator;

  /**
   * Override the base constructor.
   *
   * @param $environment
   *  The current environment handler.
   * @param $component_type
   *  A component type. Currently supports 'module' and 'theme'.
   *  (We need this early on so we can use it to determine our sanity level.)
   */
  public function __construct($environment, $component_type) {
    $this->environment = $environment;

    $this->initGenerators();

    // The component name is just the same as the type for the base generator.
    $component_name = $component_type;

    $this->base = $component_type;
    $this->base_generator = $this->getGenerator($component_type, $component_name);
  }

  /**
   * Get the sanity level this task requires.
   *
   * We override this to hand over to the base generator, as different bases
   * may have different requirements.
   *
   * @return
   *  A sanity level string to pass to the environment's verifyEnvironment().
   */
  function getSanityLevel() {
    return $this->base_generator->sanity_level;
  }

  /**
   * Helper to perform setup tasks: register autoloader.
   */
  private function initGenerators() {
    // Register our autoload handler for generator classes.
    spl_autoload_register(array($this, 'generatorAutoload'));
  }

  /**
   * Generate the files for a component.
   *
   * This is the entry point for the generating system.
   *
   * (Replaces module_builder_generate_component().)
   *
   * @param $component_data
   *  An associative array of data for the component. Values depend on the
   *  component class. For details, see the constructor of the generator, of the
   *  form ModuleBuider\Generator\COMPONENT, e.g.
   *  ModuleBuider\Generator\Module::__construct().
   *
   * @return
   *  A files array whose keys are filepaths (relative to the module folder) and
   *  values are the code destined for each file.
   */
  public function generateComponent($component_data) {
    // Add the top-level component to the data.
    $component_data['base'] = $this->base;

    // Set the component data on the base generator.
    $this->base_generator->component_data = $component_data;

    // Recursively get subcomponents.
    $this->base_generator->getSubComponents();

    //drush_print_r($generator->components);

    // Recursively build files.
    $files = array();
    $this->base_generator->collectFiles($files);
    //drush_print_r($files);

    $files_assembled = $this->base_generator->assembleFiles($files);

    return $files_assembled;
  }

  /**
   * Generator factory.
   *
   * @param $component_type
   *   The type of the component. This is use to build the class name: see
   *   getGeneratorClass().
   * @param $component_name
   *   The identifier for the component. This is often the same as the type
   *   (e.g., 'module', 'hooks') but in the case of types used multiple times
   *   this will be a unique identifier.
   *
   * @return
   *   A generator object, with the component name and data set on it, as well
   *   as a reference to this task handler.
   */
  public function getGenerator($component_type, $component_name) {
    $class = $this->getGeneratorClass($component_type);
    $generator = new $class($component_name);

    // Each generator needs a link back to the factory to be able to make more
    // generators, and also so it can access the environment.
    $generator->task = $this;
    $generator->base_component = $this->base_generator;

    return $generator;
  }

  /**
   * Helper function to get the desired Generator class.
   *
   * @param $type
   *  The type of the component. This is used to determine the class.
   *
   * @return
   *  A class name for the type and, if it exists, version, e.g.
   *  'ModuleBuider\Generator\Info6'.
   *
   * @see Generate::generatorAutoload()
   */
  private function getGeneratorClass($type) {
    // Include our general include files, which contains base and parent classes.
    $file_path = $this->environment->getPath("Generator/Base.php");
    include_once($file_path);

    $type     = ucfirst($type);
    $version  = $this->environment->major_version;
    $class    = 'ModuleBuider\\Generator\\' . $type . $version;

    // Trigger the autoload for the base name without the version, as all versions
    // are in the same file.
    class_exists('ModuleBuider\\Generator\\' . $type);

    // If there is no version-specific class, use the base class.
    if (!class_exists($class)) {
      $class  = 'ModuleBuider\\Generator\\' . $type;
    }
    return $class;
  }

  /**
   * Autoload handler for generator classes.
   *
   * @see getGeneratorClass()
   */
  function generatorAutoload($class) {
    // Generator classes are in standardly named files, with all versions in the
    // same file.
    list($module_builder, $generator, $class) = explode('\\', $class);
    $file_path = $this->environment->getPath("Generator/$class.php");
    if (file_exists($file_path)) {
      include_once($file_path);
    }
  }

}
