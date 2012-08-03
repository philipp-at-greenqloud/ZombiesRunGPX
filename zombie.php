<?php
        
  header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=file.csv");
header("Pragma: no-cache");
header("Expires: 0");

    include_once('simplehtmldom/simple_html_dom.php');

    
    //$url = 'https://www.zombiesrungame.com/kruemelnerd/178654/';


    
    $url = $_GET['url'];

    $complete_file = '<?xml version="1.0" encoding="UTF-8"?>';
    $complete_file .= '<gpx version="1.1" creator="philippveit.de">';
    $complete_file .= '  <trk>';
    $complete_file .= '    <type>RUNNING</type>';
    $complete_file .= '    <trkseg>';





    // Create DOM from URL or file
    $html = file_get_html($url);

    $property_name = 'property';
    $content_name = 'content';

    $time_start;
    $time_end;
    $time_complete;

    $numbers_of_waypoints = 0;
    // Find all links, and their text
    foreach($html->find('meta') as $elm) {


        $attribute = $elm->getAttribute ( $property_name ); 
                // <meta property="zombiesrun:started"  content="2012-07-16T21:54:30+00:00" />      ->     1342475670
                // <meta property="zombiesrun:ended"  content="2012-07-16T22:38:50+00:00" />        ->     1342478330
                
            if($attribute=='zombiesrun:started'){
                 //$time_start = new DateTime($elm->getAttribute ( $content_name ));
                              $time_start = strtotime($elm->getAttribute ( $content_name ));
                
                //$time_start = $elm->getAttribute ( $content_name ); 
                //      echo "Run starteted at ", $time_start, "<br />";
               
            }
            
            if($attribute=='zombiesrun:ended'){
                $time_end = strtotime($elm->getAttribute ( $content_name )); 
//                echo "Run endeded at ", $time_end, "<br />";
            }



           if($attribute=='zombiesrun:route:longitude'){
                //echo '<trkpt ';
    //            echo ' lon : ';
    //            echo $elm->getAttribute ( $content_name );
                
            }
            if($attribute=='zombiesrun:route:latitude'){
                $numbers_of_waypoints = $numbers_of_waypoints+1;            
    //            echo ' lat : ';
    //            echo $elm->getAttribute ( $content_name );
    //            echo '<br />';

                    //echo '></trkpt><time></time><br />';
            }

          
    //                  echo $elm->getAttribute ( $property_name )  .' ('.$elm->plaintext. ')<br/>';
    //        echo $elm->getAttribute ( $content_name )   .' ('.$elm->plaintext. ')<br/>';
        
    }
    //echo "$numbers_of_waypoints  Numbers of Waypoints<br />";

    $time_complete = $time_end - $time_start;
    //echo "$time_complete in unixtime needed for the run<br />";


    $time_complete_normal = date("H.i.s",$time_complete);
    //echo "$time_complete_normal in normal needed for the run<br />";

    $time_per_waypoint = $time_complete / $numbers_of_waypoints;
    $time_per_waypoint_normal = date("H.i.s",$time_per_waypoint);
    //echo "$time_per_waypoint_normal  in normal needed for one waypoint<br />";

    $html = file_get_html($url);
    $numbers_of_waypoints=0;
    $one_line = "";
    foreach($html->find('meta') as $elm) {


        $attribute = $elm->getAttribute ( $property_name ); 
                

           if($attribute=='zombiesrun:route:longitude'){
                $one_line .= "<trkpt";
                
                
                $one_line .=' lon="';
                $one_line .= $elm->getAttribute ( $content_name );
                            $one_line .='"';
            }
            if($attribute=='zombiesrun:route:latitude'){
                $numbers_of_waypoints = $numbers_of_waypoints+1;
                            
                $one_line .=' lat="';
                $one_line .=$elm->getAttribute ( $content_name );
                $one_line .='"';

                $one_line .='><time>';
                $time_at_this_point = ($time_per_waypoint * $numbers_of_waypoints) + $time_start;
                //echo $time_at_this_point, '<br />';
                
                //2012-07-16T21:54:30+00:00
                $one_line .=date("Y-m-dTH:i:s+00:00",$time_at_this_point);
                $one_line .= '</time>';
                 $one_line .= '</trkpt>';
                 $complete_file .= $one_line;
                 //echo htmlentities($one_line),"<br />";
                 $one_line = "";
            }     
        
    }


    $complete_file .= '    </trkseg>';
    $complete_file .= '  </trk>';
    $complete_file .= '</gpx>';
   
    
  //  $filename = "zombie.gpx";

    //header("Cache-Control: cache, must-revalidate");
    //header("Content-type: application/force-download");
    //header('Content-Disposition: attachment; filename="zombie.txt"');
    //header("Pragma: public");
    //header("Content-type: application/octet-stream");
    //header("Content-Disposition: attachment; filename=test.dat");


    echo htmlentities($complete_file); 


?>