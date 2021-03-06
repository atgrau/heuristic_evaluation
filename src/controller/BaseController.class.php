<?php

  /**
   * Base Controller
   */
  class BaseController
  {
    private $view = "index";
    private $content;
    private $breadcrumb = array();

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

    function setBreadcrumb($value) {
      $this->breadcrumb = $value;
    }

    function getBreadcrumb() {
      if ($this->breadcrumb) {
        $output .= '<li class="breadcrumb-item active" aria-current="page"><a href="/">Home</a></li>';

        foreach ($this->breadcrumb as $crumb) {
          if ($crumb[1]) {
            $output .= '<li class="breadcrumb-item"><a title="'.$crumb[0].'" href="'.$crumb[1].'">'.$crumb[0].'</a></li>';
          } else {
            $output .= '<li class="breadcrumb-item">'.$crumb[0].'</li>';
          }
        }
      } else {
        $output .= '<li class="breadcrumb-item"><strong>'.$GLOBALS["USER_SESSION"]->getName().'</strong>, welcome to '.APP_TITLE.'.</li>';
      }

      return $output;
    }

    function editContent() {
      if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if ($_POST["entry"] == "home") {
          DB::update('content_entries', array(
            "content" => $_POST["content"]
          ), "ID=%s", "home");
          $this->saved = true;
          $this->tab = 0;
        } elseif ($_POST["entry"] == "about-us") {
          DB::update('content_entries', array(
            "content" => $_POST["content"]
          ), "ID=%s", "about-us");
          $this->saved = true;
          $this->tab = 1;
        } elseif ($_POST["entry"] == "evaluation") {
          DB::update('content_entries', array(
            "content" => $_POST["content"]
          ), "ID=%s", "evaluation");
          $this->saved = true;
          $this->tab = 2;
        }

      }
      $this->render();
    }

    function render() {
      include(BASE_URI."view/".$this->view.".php");
      exit;
    }

    function renderContent() {
      include(BASE_URI."view/".$this->content.".php");
      exit;
    }

    function getHomeContent() {
      $content = DB::queryFirstRow("SELECT content FROM content_entries WHERE ID=%s", "home");
      if ($content) {
        return $content["content"];
      } else {
        return "";
      }
    }

    function getAboutUsContent() {
      $content = DB::queryFirstRow("SELECT content FROM content_entries WHERE ID=%s", "about-us");
      if ($content) {
        return $content["content"];
      } else {
        return "";
      }
    }

    function getEvaluationContent() {
      $content = DB::queryFirstRow("SELECT content FROM content_entries WHERE ID=%s", "evaluation");
      if ($content) {
        return $content["content"];
      } else {
        return "";
      }
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
