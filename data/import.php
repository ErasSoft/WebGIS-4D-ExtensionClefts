<?php
// Configuration (password, dbname, user, password, dbschema, tmp_path, srid)
include("config.php");
?>

<!-- Formular -->
<form action="" method="post" enctype="multipart/form-data">
  <?php
  // Wenn mit iframe und GET-Parameter aufgerufen wurde
  if (isset($_GET['pw'])) {
    if ($_GET['pw'] != $password) {
  ?>  
    <b>Security password for upload:</b><br>
    <input type="password" name="password">    
    <br><br>    
  <?php
    }
  }
  else {
  ?>
    <b>Security password for upload:</b><br>
    <input type="password" name="password">    
    <br><br>    
<?php  
  }
  ?>

  <b>Data import for: </b>
  <select name="type">
    <option value="0" <?php if (isset($_POST['type'])) if ($_POST['type'] == "0") echo "selected"; ?> >Poi</option>
    <option value="1" <?php if (isset($_POST['type'])) if ($_POST['type'] == "1") echo "selected"; ?> >Polygon</option>
    <option value="2" <?php if (isset($_POST['type'])) if ($_POST['type'] == "2") echo "selected"; ?> >Person</option>
    <option value="3" <?php if (isset($_POST['type'])) if ($_POST['type'] == "3") echo "selected"; ?> >Type (Poi)</option>
  </select>

  <!-- Upload -->
  <br><br>
  <input type="file" name="upload" accept="text/*">
  <br><br>
  <input type="Submit" name="import" value="Import CSV-File">
</form>

<?php
if (isset($_POST['import']) ) {
  if ($_FILES['upload']['error'] == 0) {
    // Wenn mit iframe und GET-Parameter aufgerufen wurde
    $get_pw = false;
    if (isset($_GET['pw'])) {
      if ($_GET['pw'] == $password) {
        $get_pw = true;
      }
    }
    if ($_POST['password'] == $password || $get_pw) {
      if (move_uploaded_file($_FILES['upload']['tmp_name'], $tmp_path.$_FILES['upload']['name'] ) ) {

        // Verbindungsaufbau und Auswahl der Datenbank
        $dbconn = pg_connect("host=localhost dbname=$dbname user=$dbuser password=$dbpassword")
          or die('Verbindungsaufbau fehlgeschlagen: ' . pg_last_error());

        // Delete import_table
        $query = 'DROP TABLE '.$dbschema.'.import_table';
        $result = pg_query($query);

        // SQL-Abfragen ausführen
        if ($_POST['type'] == 0) {
          // create new table import_table
          $query =  'CREATE TABLE '.$dbschema.'.import_table (
                       id INTEGER,
                       collected DATE,
                       typeid INTEGER,
                       personid INTEGER,
                       x DOUBLE PRECISION,
                       y DOUBLE PRECISION,
                       z DOUBLE PRECISION
                     )';
          $result = pg_query($query) or die('Abfrage fehlgeschlagen: ' . pg_last_error());

          // import csv data to import_table
          $query = "COPY ".$dbschema.".import_table
                    FROM '".$tmp_path.$_FILES['upload']['name']."'
                    WITH
                      DELIMITER AS ';'
                      CSV HEADER";
          $result = pg_query($query) or die('Abfrage fehlgeschlagen: ' . pg_last_error());

          // copy data from import_table to person
          $query = 'INSERT INTO '.$dbschema.'.poi (id, collected, typeid, personid, geom)
                      SELECT id, collected, typeid, personid,
                        ST_SetSRID(ST_MakePoint(x,y,z),'.$srid.')
                      FROM '.$dbschema.'.import_table';
          $result = pg_query($query) or die('Abfrage fehlgeschlagen: ' . pg_last_error());
        }
        elseif ($_POST['type'] == 1) {
          // create new table import_table
          $query =  'CREATE TABLE '.$dbschema.'.import_table (
                       id INTEGER,
                       collected DATE,
                       orientation FLOAT,
                       openingmax FLOAT,
                       openingmin FLOAT,
                       depth FLOAT,
                       personid INTEGER,
                       edges CHARACTER VARYING
                     )';
          $result = pg_query($query) or die('Abfrage fehlgeschlagen: ' . pg_last_error());

          // import csv data to import_table
          $query = "COPY ".$dbschema.".import_table
                    FROM '".$tmp_path.$_FILES['upload']['name']."'
                    WITH
                      DELIMITER AS ';'
                      CSV HEADER";
          $result = pg_query($query) or die('Abfrage fehlgeschlagen: ' . pg_last_error());

          // copy data from import_table to person
          $query = "INSERT INTO ".$dbschema.".polygon (id, collected, orientation, openingmax, openingmin, depth, personid, geom)
                      SELECT id, collected, orientation, openingmax, openingmin, depth, personid,
                        ST_GeomFromText(CONCAT('POLYGON((', CONCAT(edges, '))')),".$srid.")
                      FROM ".$dbschema.".import_table";
          $result = pg_query($query) or die('Abfrage fehlgeschlagen: ' . pg_last_error());

        }
        elseif ($_POST['type'] == 2) {
          // create new table import_table
          $query =  'CREATE TABLE '.$dbschema.'.import_table (
                       firstname CHARACTER VARYING,
                       lastname CHARACTER VARYING,
                       email CHARACTER VARYING,
                       phone CHARACTER VARYING
                     )';
          $result = pg_query($query) or die('Abfrage fehlgeschlagen: ' . pg_last_error());

          // import csv data to import_table
          $query = "COPY ".$dbschema.".import_table
                    FROM '".$tmp_path.$_FILES['upload']['name']."'
                    WITH
                      DELIMITER AS ';'
                      CSV HEADER";
          $result = pg_query($query) or die('Abfrage fehlgeschlagen: ' . pg_last_error());

          // copy data from import_table to person
          $query = 'INSERT INTO '.$dbschema.'.person (firstname, lastname, email, phone)
                      SELECT firstname, lastname, email, phone
                      FROM '.$dbschema.'.import_table';
          $result = pg_query($query) or die('Abfrage fehlgeschlagen: ' . pg_last_error());
        }
        elseif ($_POST['type'] == 3) {
          // create new table import_table
          $query = 'CREATE TABLE '.$dbschema.'.import_table (name CHARACTER VARYING)';
          $result = pg_query($query) or die('Abfrage fehlgeschlagen: ' . pg_last_error());

          // import csv data to import_table
          $query = "COPY ".$dbschema.".import_table
                    FROM '".$tmp_path.$_FILES['upload']['name']."'
                    WITH
                      DELIMITER AS ';'
                      CSV HEADER";
          $result = pg_query($query) or die('Abfrage fehlgeschlagen: ' . pg_last_error());

          // copy data from import_table to person
          $query = 'INSERT INTO '.$dbschema.'.type (name)
                      SELECT name
                      FROM '.$dbschema.'.import_table';
          $result = pg_query($query) or die('Abfrage fehlgeschlagen: ' . pg_last_error());

        }

        // Success
        echo "Upload successful<br><br>";

        // Show data for personid, typeid
        if ($_POST['type'] == 2) {
          $query = 'SELECT * FROM '.$dbschema.'.person';
          $result = pg_query($query) or die('Abfrage fehlgeschlagen: ' . pg_last_error());

          // Ergebnisse in HTML ausgeben
          echo "<table border='0' cellpadding='3'>\n";
          echo "\t<tr>\n";
          echo "\t\t<td>id</td>  <td>firstname</td>  <td>lastname</td>\n";
          echo "\t</tr>\n";
          while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
            echo "\t<tr>\n";
            echo "\t\t<td>".$line['id']."</a></td>\n";
            echo "\t\t<td>".$line['firstname']."</td>\n";
            echo "\t\t<td>".$line['lastname']."</td>\n";            
            echo "\t</tr>\n";
          }
          echo "</table>\n";
        }
        elseif ($_POST['type'] == 3) {
          $query = 'SELECT * FROM '.$dbschema.'.type';
          $result = pg_query($query) or die('Abfrage fehlgeschlagen: ' . pg_last_error());

          // Ergebnisse in HTML ausgeben
          echo "<table border='0' cellpadding='2'>\n";
          echo "\t<tr>\n";
          echo "\t\t<td>id</td>  <td>type</td>\n";
          echo "\t</tr>\n";
          while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
            echo "\t<tr>\n";
            echo "\t\t<td>".$line['id']."</a></td>\n";
            echo "\t\t<td>".$line['name']."</td>\n";
            echo "\t</tr>\n";
          }
          echo "</table>\n";

        }

        // Delete import_table
        $query = 'DROP TABLE '.$dbschema.'.import_table';
        $result = pg_query($query) or die('Abfrage fehlgeschlagen: ' . pg_last_error());

        // Delete upload-file
        unlink($tmp_path.$_FILES['upload']['name']);

        // Speicher freigeben
        pg_free_result($result);

        // Verbindung schließen
        pg_close($dbconn);
      }
      else {
        echo "Error: Copy to path (".$tmp_path.") failed!";
      }
    }
    else {
      echo "Error: Wrong password!";
    }
  }
  else {
    if ($_FILES['upload']['error'] == 4) {
      echo "Error: No file was uploaded.";
    }
    else {
      echo "Error: Code ".$_FILES['upload']['error'];
    }
  }
}
else {
  echo "
  <u><b>CSV-Format:</b></u><br>
  <b>Poi:</b><br>
  id; collected; typeid; personid; x; y; z <br>
  <b>Polygon:</b><br>
  id; collected; orientation; openingmax; openingmin; depth; personid; edges <br>
  <b>Person:</b><br>
  person <br>
  <b>Type (Poi):</b><br>
  type <br>";
}
?>
