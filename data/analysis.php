<?php 
// Configuration (url, dbname, dbuser, dbpassword, dbschema)
include("config.php");
?>

<!-- Formular -->
<form action="" method="post">
  <b>Analysis method: </b>
  <select name="type">
    <option value="0" <?php if (isset($_POST['type'])) if ($_POST['type'] == "0") echo "selected"; ?> >Maximal</option>
    <option value="1" <?php if (isset($_POST['type'])) if ($_POST['type'] == "1") echo "selected"; ?> >Yearly</option>
    <option value="2" <?php if (isset($_POST['type'])) if ($_POST['type'] == "2") echo "selected"; ?> >Average yearly</option>
  </select>

  <p>
  <b>Poi distance [m] greater then:</b>
  <input type="number" step="any" name="distance" value="<?php if (isset($_POST['search_poi']) ) echo $_POST['distance']; ?>" size="" maxlength="">
  <input type="Submit" name="search_poi" value="search">
  </p>

  <p>
  <b>Polygon area [m&sup2;] greater then:</b>
  <input type="number" step="any" name="area" value="<?php if (isset($_POST['search_polygon'])) echo $_POST['area']; ?>" size="" maxlength="">
  <input type="Submit" name="search_polygon" value="search">
  </p>
</form>


<?php
// Datenbank-Verbindung - Quelle: http://php.net/manual/de/pgsql.examples-basic.php
if (isset($_POST['search_poi'])) {
  if ($_POST['search_poi'] == "search" && $_POST['distance'] > 0) {
    // Verbindungsaufbau und Auswahl der Datenbank
    $dbconn = pg_connect("host=localhost dbname=$dbname user=$dbuser password=$dbpassword")
      or die('Verbindungsaufbau fehlgeschlagen: ' . pg_last_error());

    // Eine SQL-Abfrge ausführen
    if ($_POST['type'] == 0) { $field = 'distance'; $table = $dbschema.'.poi_max_distance'; }
    elseif ($_POST['type'] == 1) { $field = 'distance'; $table = $dbschema.'.poi_year_distance_avg'; }
    elseif ($_POST['type'] == 2) { $field = 'distance_avg'; $table = $dbschema.'.poi_year_distance_avg'; }

    $query = 'SELECT x, y, id, collected_from, collected, '.$field.' ';
    $query .= 'FROM '.$table.' ';
    $query .= 'WHERE '.$field.' > '.$_POST['distance'];
    if ($_POST['type'] == 0)     $query .= 'AND nid = 2';
    elseif ($_POST['type'] == 2) $query .= 'AND distance IS NULL';

    $result = pg_query($query) or die('Abfrage fehlgeschlagen: ' . pg_last_error());

    // Ergebnisse in HTML ausgeben
    echo "<table border='0' cellpadding='5'>\n";
    echo "\t<tr>\n";
    if ($_POST['type'] < 2)
      echo "\t\t<td>id</td>  <td>from</td> <td>to</td> <td>distance [m]</td>\n";
    else
      echo "\t\t<td>id</td>  <td>distance [m]</td>\n";

    echo "\t</tr>\n";
    while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
      echo "\t<tr>\n";
      echo "\t\t<td><a href=\"".$url."?poi%5Bpoint%5D=".$line['x']."%2C".$line['y']."\" target=\"_blank\">".$line['id']."</a></td>\n";
      if ($_POST['type'] < 2) {
        echo "\t\t<td>".$line['collected_from']."</td>\n";
        echo "\t\t<td>".$line['collected']."</td>\n";
      }
      echo "\t\t<td>";
      if ($_POST['type'] < 2)
        echo $line['distance'];
      else
        echo $line['distance_avg'];
      echo "</td>\n";
      echo "\t</tr>\n";
    }
    echo "</table>\n";

    // Speicher freigeben
    pg_free_result($result);

    // Verbindung schließen
    pg_close($dbconn);

  }
}
if (isset($_POST['search_polygon'])) {
  if ($_POST['search_polygon'] == "search" && $_POST['area'] > 0) {
    // Verbindungsaufbau und Auswahl der Datenbank
    $dbconn = pg_connect("host=localhost dbname=$dbname user=$dbuser password=$dbpassword")
      or die('Verbindungsaufbau fehlgeschlagen: ' . pg_last_error());

    // Eine SQL-Abfrge ausführen
    if ($_POST['type'] == 0) { $field = 'difference_area'; $table = $dbschema.'.polygon_max_distance'; }
    elseif ($_POST['type'] == 1) { $field = 'difference_area'; $table = $dbschema.'.polygon_year_distance_avg'; }
    elseif ($_POST['type'] == 2) { $field = 'difference_area_avg'; $table = $dbschema.'.polygon_year_distance_avg'; }

    if ($_POST['type'] == 0)
      $query = 'SELECT centroid_x, centroid_y, id, collected_from AS collected, collected AS collected_from, '.$field.' ';
    else
      $query = 'SELECT centroid_x, centroid_y, id, collected_from, collected, '.$field.' ';
    $query .= 'FROM '.$table.' ';
    $query .= 'WHERE '.$field.' > '.$_POST['area'];
    if ($_POST['type'] == 0) $query .= 'AND nid=1';
    elseif ($_POST['type'] == 2) $query .= 'AND difference_area IS NULL';

    $result = pg_query($query) or die('Abfrage fehlgeschlagen: ' . pg_last_error());

    // Ergebnisse in HTML ausgeben
    echo "<table border='0' cellpadding='5'>\n";
    echo "\t<tr>\n";
    if ($_POST['type'] < 2)
      echo "\t\t<td>id</td>  <td>from</td> <td>to</td> <td>area [m&sup2;]</td>\n";
    else
      echo "\t\t<td>id</td>  <td>area [m&sup2;]</td>\n";
    echo "\t</tr>\n";
    while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
      echo "\t<tr>\n";
      echo "\t\t<td><a href=\"".$url."?poi%5Bpoint%5D=".$line['centroid_x']."%2C".$line['centroid_y']."\" target=\"_blank\">".$line['id']."</a></td>\n";
      if ($_POST['type'] < 2) {
        echo "\t\t<td>".$line['collected_from']."</td>\n";
        echo "\t\t<td>".$line['collected']."</td>\n";
      }
      echo "\t\t<td>";
      if ($_POST['type'] < 2)
        echo $line['difference_area'];
      else
        echo $line['difference_area_avg'];
      echo "</td>\n";
      echo "\t</tr>\n";
    }
    echo "</table>\n";

    // Speicher freigeben
    pg_free_result($result);

    // Verbindung schließen
    pg_close($dbconn);

  }
}
?>
