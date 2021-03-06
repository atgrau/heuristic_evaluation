<?php

  class ProjectController extends BaseController
  {
    function __construct() { }

    function showProjectList($admin) {
      $this->admin = $admin;

      // Breadcrumb
      if ($admin) {
        $this->setBreadcrumb(array(
            array("Projects")
        ));
      } else {
        $this->setBreadcrumb(array(
            array("My Projects")
        ));
      }


      $this->setContentView("project/projectlist");
      $this->query = $_GET["q"];
      $this->userId = $_GET["user"];
      $this->edit = true;
      if (!$admin) {
        $this->projectList = getMyProjects($GLOBALS["USER_SESSION"]->getId(), $this->query);
        $this->archivedProjectList = getMyArchivedProjects($GLOBALS["USER_SESSION"]->getId(), $this->query);
      } else {
        $this->projectList = getProjects($this->query, $this->userId);
      }
      $this->render();
    }

    function showAssignedProjectList() {
      // Breadcrumb
      $this->setBreadcrumb(array(
          array("Project Evaluations")
      ));

      $this->setContentView("project/projectlist");
      $this->query = $_GET["q"];
      $this->userId = $_GET["user"];
      $this->projectList = getAssignedProjects($GLOBALS["USER_SESSION"]->getId(), $this->query);
      $this->edit = false;
      $this->render();
    }

    function addNewProjectView() {
      $this->setBreadcrumb(array(
          array("My Projects","/my-projects"),
          array("New project")
      ));

      $this->templateList = getActiveTemplates();
      $this->new = true;
      $this->setContentView("project/project");
      $this->render();
    }

    function addNewProject() {
      if ($_SERVER["REQUEST_METHOD"] != "POST") {
        header("Location: /my-projects");
      }

      try {
        $date = new DateTime($_POST["finish_date"]);
      }  catch (Exception $e) {
        $date = new DateTime(now());
      }

      $project = new Project(0, $GLOBALS["USER_SESSION"]->getId(), null, $date, $_POST["name"], $_POST["description"], $_POST["link"], false, getTemplateById($_POST["template"]), false);
      $users = array();
      if ($_POST["users"]):
        foreach ($_POST["users"] as $user) {
          array_push($users, getUserById($user));
        }
        $project->setUsers($users);
      endif;

      // Validate Project
      $this->validateProject($project, 0, false);

      // Insert into database
      $project->insert();

      if ($_POST["email"] == "1") {
        foreach ($project->getUsers() as $user) {
          // Send Email to the users
          $subject = "You have been assigned to a new project";
          $body = "Hello <strong>".$user->getName()."</strong>, <br /><br />";
          $body .= "You have been assigned to a new project:<br />";
          $body .= "<ul>";
          $body .= "<li><strong>Name:</strong> ".$project->getName()."</li>";
          $body .= "<li><strong>Description:</strong> ".$project->getDescription()."</li>";
          $body .= "<li><strong>Link:</strong> ".$project->getLink()."</li>";
          $body .= "</ul>";
          $body .= "<br />You can evaluate the project here:<br /><a href='".URL."evaluations/id/".$project->getId()."'>".URL."projects/evaluation/".$project->getId()."</a><br /><br />";

          $email = new Email($user->getEmail(), $subject, $body);
          $email->send();
        }
      }

      $this->addMessage = true;
      $this->recentProject = $_POST["name"];
      $this->showProjectList(false);
    }

    function updateProjectView($adminView, $projectId) {
      $project = getProjectById($projectId);

      if ((!$project) || ((!$GLOBALS["USER_SESSION"]->isAdmin()) && ($GLOBALS["USER_SESSION"]->getId() != $project->getUser()->getId()))){
          $this->showProjectList($adminView);
      } else {

        if ($adminView) {
          $this->setBreadcrumb(array(
              array("Projects","/admin/projects"),
              array($project->getName())
          ));
        } else {
          $this->setBreadcrumb(array(
              array("My Projects","/my-projects"),
              array($project->getName())
          ));
        }


        $this->setContentView("project/project");
        $this->project = $project;
        $this->adminView = $adminView;
        $this->templateList = getTemplates("");
        $this->render();
      }
    }

    function updateProject($adminView) {
      if ($_SERVER["REQUEST_METHOD"] != "POST") {
        header("Location: /");
      }

      $project = getProjectById($_POST["id"]);
      if ((!$project) || ((!$GLOBALS["USER_SESSION"]->isAdmin()) && ($GLOBALS["USER_SESSION"]->getId() != $project->getUser()->getId()))){
        header("Location: /");
      } else {
        $project->setName($_POST["name"]);
        $project->setDescription($_POST["description"]);
        $project->setLink($_POST["link"]);
        try {
          $project->setFinishDate(new DateTime($_POST["finish_date"]));
        } catch (Exception $e) { }
        $originalTemplateId = $project->getTemplate()->getId();
        $newTemplate = getTemplateById($_POST["template"]);
        if ($newTemplate) {
          $project->setTemplate($newTemplate);
        }

        $project->setArchived(($_POST["archived"] == "1"));

        if ($adminView) {
          $project->setActive($_POST["active"]=="1");
        }

        $users = array();
        if ($_POST["users"]):
          foreach ($_POST["users"] as $user) {
            array_push($users, getUserById($user));
          }
          $project->setUsers($users);
        else:
          $project->setUsers(array());
        endif;

        // Validate Project
        $this->validateProject($project, $originalTemplateId, $adminView);

        // Update Project
        $project->update();

        if ($_POST["email"] == "1") {
          foreach ($project->getUsers() as $user) {
            // Send Email to the users
            $subject = "A project that you have been assigned in was modified";
            $body = "Hello <strong>".$user->getName()."</strong>, <br /><br />";
            $body .= "You are assigned in a project which has been modified, check the newer information:<br />";
            $body .= "<ul>";
            $body .= "<li><strong>Name:</strong> ".$project->getName()."</li>";
            $body .= "<li><strong>Description:</strong> ".$project->getDescription()."</li>";
            $body .= "<li><strong>Link:</strong> ".$project->getLink()."</li>";
            $body .= "</ul>";
            $body .= "<br />You can evaluate the project here:<br /><a href='".URL."evaluations/id/".$project->getId()."'>".URL."projects/evaluation/".$project->getId()."</a><br /><br />";

            $email = new Email($user->getEmail(), $subject, $body);
            $email->send();
          }
        }

        $this->editMessage = true;
        $this->recentProject = $_POST["name"];
        $this->showProjectList($adminView);
      }
    }

    function validateProject($project, $originalTemplateId, $adminView) {
      $validate = "";
      if (strlen($project->getName()) > 50) {
        $validate .= "<li>Project's name cannot contain more than 50 characters.</li>";
      }

      if (strlen($project->getName()) == 0) {
        $validate .= "<li>Project's name cannot be empty.</li>";
      }

      if (strlen($project->getDescription()) > 1000) {
        $validate .= "<li>Project's description cannot contain more than 1000 characters.</li>";
      }

      if (strlen($project->getDescription()) == 0) {
        $validate .= "<li>Project's description cannot be empty.</li>";
      }

      if (strlen($project->getLink()) > 100) {
        $validate .= "<li>Project's link cannot contain more than 100 characters.</li>";
      }

      if (strlen($project->getLink()) == 0) {
        $validate .= "<li>Project's link cannot be empty.</li>";
      }

      if (!$project->getTemplate()) {
        $validate .= "<li>Selected template is not valid.</li>";
      }

      if (($originalTemplateId != 0) && ($originalTemplateId != $project->getTemplate()->getId()) && (!$project->canReassignTemplate())) {
        $validate .= "<li>Template cannot be reassigned, due to one or more evaluations are already open.</li>";
      }

      if ($validate) {
          $this->error = $validate;
          if ($project->getId() == 0) {
            $this->addNewProjectView();
          } else {
            $this->updateProjectView($adminView, $project->getId());
          }

      }
    }

    function removeProject($adminView) {
      // Getting POST paramters
      $project = getProjectById($_GET["param"]);
      if (!$project) {
        $this->showProjectList($adminView);
      }

      // Delete all evaluations
      foreach ($project->getEvaluations() as $evaluation) {
        $evaluation->delete();
      }


      // Delete Project
      $project->delete();

      //Render View
      $this->removeMessage = true;
      $this->recentProject = $project->getName();
      $this->showProjectList($adminView);
    }
  }

?>
