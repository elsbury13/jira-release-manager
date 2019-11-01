<?php

declare(strict_types = 1);

require_once 'functions.php';

$functions = new Functions();

if (isset($_POST['createRelease'])) {
    $data = [
        'startDate' => (new DateTime(date('d M Y')))->format('c'),
        'archived' => false,
        'name' => 'release/x/' . $_POST['releaseType'],
        'description' => $_POST['description'],
        'projectId' => $_POST['project'],
        'released' => false,
    ];

    if (!empty($_POST['releaseDate'])) {
        $data['releaseDate'] = (new DateTime($_POST['releaseDate']))->format('c');
    }

    $release = json_decode(
        $functions->curlRequest(
            json_encode($data),
            'version',
            'POST'
        )
    );

    $release = json_decode(
        $functions->curlRequest(
            json_encode([
                'archived' => false,
                'name' => 'release/' . $release->id . '/' . $_POST['releaseType'],
                'projectId' => $_POST['project'],
                'released' => false,
            ]),
            'version/' . $release->id,
            'PUT'
        )
    );

    if ($release) {
        echo 'Release <strong>' . $release->name . '</strong> has been created';
    }
}

if (isset($_POST['updateRelease'])) {
    $release = explode('---', $_POST['release']);
    $data = [
        'archived' => false,
        'name' => 'release/' . $release[1] . '/' . $_POST['releaseType'],
        'projectId' => $release[0],
        'released' => false,
        'description' => $_POST['description'],
    ];

    if (!empty($_POST['releaseDate'])) {
        $data['releaseDate'] = (new DateTime($_POST['releaseDate']))->format('c');
    }

    $newRelease =json_decode(
        $functions->curlRequest(
            json_encode($data),
            'version/' . $release[1],
            'PUT'
        )
    );

    if ($newRelease) {
        echo 'Release <strong>' . $newRelease->name . '</strong> has been updated';
    }
}

$projects = json_decode($functions->getAll('project'), true);

?>
<h1>Create A Jira Release</h1>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
  Release Type:<br />
  <select name="releaseType">
    <option disabled selected>Please select a release type</option>
    <option value="configuration">Configuration</option>
    <option value="devsuite">Devsuite</option>
    <option value="feature">Feature</option>
    <option value="hotfix">Hotfix</option>
  </select>
  <br /><br />Description:<br />
  <textarea cols="50" rows="5" name="description" value=""></textarea>
  <br /><br />Release Date:<br />
  <input type="date" name="releaseDate">
  <br /><br />Project:<br />
  <select name="project">
    <option disabled selected>Please select a project</option>
    <?php
    foreach ($projects as $project) {
        echo '<option value="' . $project['id'] . '">' . $project['name'] . '</option>';
    }
    ?>
  </select>
  <br /><br />
  <input type="submit" name="createRelease" value="Submit">
</form>
<hr>

<h1>Update A Jira Release</h1>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
  Release Type:<br />
  <select name="releaseType">
    <option disabled selected>Please select a release type</option>
    <option value="configuration">Configuration</option>
    <option value="devsuite">Devsuite</option>
    <option value="feature">Feature</option>
    <option value="hotfix">Hotfix</option>
  </select>
  <br /><br />Description:<br />
  <textarea cols="50" rows="5" name="description" value=""></textarea>
  <br /><br />Release Date:<br />
  <input type="date" name="releaseDate">
  <br /><br />Release:<br />
  <select name="release">
    <option disabled selected>Please select a release</option>
    <?php
    foreach ($functions->getReleases($projects) as $version => $name) {
        echo '<option value="' . $version . '">' . $name . '</option>';
    }
    ?>
  </select>
  <br /><br />
  <input type="submit" name="updateRelease" value="Submit">
</form>
