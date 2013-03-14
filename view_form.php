<?php
/***************************************************

 Smooth Student Request System (SSRS)

 This file is part of SSRS, which is licensed
 under the Creative Commons: Attribution,
 Non-Commercial, Share Alike license (see
 http://creativecommons.org/licenses/by-nc-sa/3.0/)
 
 The first version was developed by
 Gjalt-Jorn Peters for the Dutch Open University
 in March 2013.
 
***************************************************/

  // The debug GET variable is useful for debugging: if this is set, no email is sent.
  // The admin GET variable enabled normal use of the site when it's in maintenance (see
  // index.php).
  echo('<form action="index.php?'.(isset($_GET['debug'])?"debug&":"").(isset($_GET['admin'])?"admin&":"").'action=submit" id="form" method="post" enctype="multipart/form-data">');
?>
    <div class="headingBar">Cursus en/of onderdeel:</div>
      <div class="formBlock">
        <div class="formRow">
          <select name="course">
            <option value=""></option>
<?php
  foreach ($courses as $currentCourse) {
    echo("<option value=\"{$currentCourse->id}\" ".($currentCourse->id == $course ?"selected":"").">{$currentCourse->name}</option>");
  }
?>
          </select>
        </div>
        <div class="clearFloat"></div>
      </div>
      <div class="headingBar">Gegevens:</div>
      <div class="formRow">
        <div class="formLabel">Naam:</div>
        <input class="formInput" type="text" name="name" id="name" value="<?php echo($_POST['name']); ?>"></input>
      </div>
      <div class="formRow">
        <div class="formLabel">Nummer:</div>
        <input class="formInput" type="text" name="nr" id="nr" value="<?php echo($_POST['nr']); ?>"></input>
      </div>
      <div class="formRow">
        <div class="formLabel">Email-adres:</div>
        <input class="formInput" type="text" name="email" id="email" value="<?php echo($_POST['email']); ?>"></input>
      </div>
      <div class="headingBar">Inleveren:</div>
      <br />Controleer het email adres, en zorg dat deze klopt alvorens dit formulier in te leveren!
      <div class="formRow">
        <input type="submit" name="submit" value="Verstuur"></input>
      </div>
    </div>
  </form>
