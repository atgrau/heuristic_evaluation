<?php

  /**
   * Base Controller
   */
  class BaseController
  {
    private $view = "index";
    private $content;

    function __construct($view, $content) {
      $this->view = $view;
      $this->content = $content;
    }

    function setView($view) {
      $this->view = $view;
    }

    function setContent($content) {
      $this->content = $content;
    }

    function setContentView($content) {
      $this->view = "index";
      $this->content = $content;
    }

    function render() {
      include(BASE_URI."view/".$this->view.".php");
      exit;
    }

    /*
    * For Navigation Menu
    */
    function getMyAssignedProjects() {
      return getAssignedProjects($GLOBALS["USER_SESSION"]->getId(), "");
    }

    function numberOfEvaluations() {
      $projects = getAssignedProjects($GLOBALS["USER_SESSION"]->getId(), "");
      if ($projects) return count($projects);
      else return 0;
    }
  }

?>
