<?php
function generateModal($project, $admin) {
  if ($admin) {
    $linkRemove = "<a href='/admin/project-remove/".$project->getId()."' type='button' class='btn btn-danger'>Delete Project</a>";
  } else {
    $linkRemove = "<a href='/my-projects/remove/".$project->getId()."' type='button' class='btn btn-danger'>Delete Project</a>";
  }
  return '<!-- Modal -->
  <div class="modal fade" id="deletingModal_'.$project->getId().'" tabindex="-1" role="dialog" aria-labelledby="deletingModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <strong class="text-danger">Project Deletion</strong>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <h5>Project <strong>'.$project->getName().'</strong> and all related evaluations <span class="bg-danger">will be removed too</span>.<br /><br />Are you sure?</h5>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          '.$linkRemove.'
        </div>
      </div>
    </div>
  </div>';
}
?>

<div class="row">
    <div class="col-lg-12">
      <?php if ($this->admin): ?>
        <h1 class="page-header">Projects</h1>
      <?php elseif ($this->edit): ?>
        <h1 class="page-header">My Projects</h1>
      <?php else: ?>
        <h1 class="page-header">Project Evaluations</h1>
      <?php endif; ?>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<?php if ($this->addMessage): ?>
  <div class="row alert alert-info" role="alert">
   <span class="glyphicon glyphicon-info-sign"></span> Project <strong><?= $this->recentProject; ?></strong> has beed added successfully!
  </div>
<?php endif; ?>
<?php if ($this->editMessage): ?>
  <div class="row alert alert-info" role="alert">
   <span class="glyphicon glyphicon-info-sign"></span> Project <strong><?= $this->recentProject; ?></strong> has beed updated successfully!
  </div>
<?php endif; ?>
<?php if ($this->removeMessage): ?>
  <div class="row alert alert-info" role="alert">
   <span class="glyphicon glyphicon-info-sign"></span> Project <strong><?= $this->recentProject; ?></strong> has beed removed successfully!
  </div>
<?php endif; ?>
<form action="" method="GET">
  <div class="row margin-lg-b">
    <div id="custom-search-input">
      <div class="input-group col-md-12">
        <input name="q" type="text" class="form-control input" placeholder="Search by name or description..." value="<?= $this->query; ?>" />
        <span class="input-group-btn">
        <button class="btn btn-primary btn" type="submit">
        <i class="glyphicon glyphicon-search"></i>
        </button>
        </span>
      </div>
    </div>
  </div>
  <?php if ($this->admin): ?>
  <div class="row margin-lg-b">
    <div id="custom-search-select">
      <div class="input-group col-md-12">
        <div class="form-group">
          <label for="user">Project Manager:</label>
          <select name="user" class="form-control" onchange="this.form.submit()">
            <option value="">Any</option>
            <?php
            foreach (getUsersByRoleGreaterThan(0) as $user) {
              if (($_GET["user"] == $user->getId())) {
                $selected = "selected";
              } else {
                $selected = "";
              }
            echo "<option value='".$user->getId()."' ".$selected.">".$user->getName()."</option>";
            }
            ?>
          </select>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>
</form>
<div class="row">
  <table class="table table-hover">
    <thead class="thead-light">
      <tr>
        <th scope="col">#</th>
        <?php if ($this->admin): ?>
          <th scope="col">Owner</th>
        <?php endif; ?>
        <th scope="col">Project's Name</th>
        <th scope="col">Description</th>
        <th scope="col">Link</th>
        <th scope="col">Finishes at</th>
        <?php if (!$this->edit): ?>
          <th scope="col">Status</th>
        <?php else: ?>
          <th scope="col"> </th>
        <?php endif; ?>
      </tr>
    </thead>
    <tbody>
      <?php
      if (!empty($this->projectList)):
      foreach ($this->projectList as $project) {
        if (!$this->edit) {
          $link = "style='cursor:pointer' class='clickable-row' data-href='/evaluations/id/".$project->getId()."'";
        } else {
          $link = "style='cursor:pointer' class='clickable-row' data-href='/my-projects/results/".$project->getId()."'";
        }
        ?>
          <tr <?=$link;?> <?php if(!$project->isActive()) echo " style='background:#EFEFEF;'"; ?>>
            <?php
              $userId = $project->getId();
              if ($this->admin) {
                $admin = "?admin=1";
              }
              echo generateModal($project, $admin);
            ?>
            <th scope="row"><?= $project->getId(); ?></th>
            <?php if ($this->admin): ?>
              <td><a href="/admin/users/<?= $project->getUser()->getId(); ?>" title="<?= $project->getUser()->getName(); ?>'s profile"><?= $project->getUser()->getFirstName(); ?></a></td>
            <?php endif; ?>
            <td><?= $project->getName(); ?></td>
            <td><?= $project->getShortDescription(); ?></td>
            <td><?= $project->getLink(); ?> <a href="<?= $project->getLink(); ?>" target="_blank" title="Link to <?= $project->getName(); ?>"><span class="glyphicon glyphicon-link"></span></a></td>
            <td><?= $project->getFinishDate(); ?></td>
            <?php if ($this->edit): ?>
              <td>
                <a href="/evaluations/result/<?= $project->getId(); ?><?php if ($this->admin) echo "?admin=1"; ?>" title="View Results"><span class="glyphicon glyphicon-eye-open"></span></a>
                <?php if ($this->admin): ?>
                  <span class="margin-l"></span>
                  <a href="/admin/projects/<?= $project->getId(); ?>" title="Edit Project"><span class="glyphicon glyphicon-pencil"></span></a>
                <?php else: ?>
                  <span class="margin-l"></span>
                  <a href="/my-projects/edit/<?= $project->getId(); ?>" title="Edit Project"><span class="glyphicon glyphicon-pencil"></span></a>
                <?php endif; ?>
                <span class="margin-l"></span>
                <a data-toggle="modal" data-target="#deletingModal_<?= $project->getId(); ?>" href="#" title="Remove Project" class="text-danger"><span class="glyphicon glyphicon-remove"></span></a>
              </td>
            <?php endif; ?>
            <?php if (!$this->edit): ?>
            <td>
              <?php
                $evaluation = getEvaluationByProjectAndUser($project->getId(), $GLOBALS["USER_SESSION"]->getId());
                if ((!$evaluation->getProject()->isClosed()) && ($evaluation->isFinished())):
                ?>
                <span class="label label-warning">Finished</span>
              <?php elseif (!$evaluation->getProject()->isClosed()): ?>
                <span class="label label-success">Open</span>
              <?php else: ?>
                <span class="label label-danger">Closed</span>
              <?php endif; ?>
            </td>
            <?php endif; ?>
          </tr>
      <?php
      }
      endif;
      ?>
    </tbody>
  </table>
  <?php
    if (empty($this->projectList)) {
      echo "<div class='alert alert-info' role='alert'>
              <span class='glyphicon glyphicon-info-sign'></span> No projects found...
            </div>";
    } else {
      echo "<strong>Total projects:</strong> ".sizeof($this->projectList);
    }
  ?>
</div>
<!-- /.row -->
<script>
jQuery(document).ready(function($) {
    $(".clickable-row").click(function() {
        window.location = $(this).data("href");
    });
});
</script>
