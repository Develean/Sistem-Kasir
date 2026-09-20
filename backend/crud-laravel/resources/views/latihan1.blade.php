<?php
// $i = 10;
// while($i > 5){
//     echo"Mejeuhh Euyy!!!!! <br>";
//     $i--;
// }

// do{
//     echo"Kukuk hela atuh KUKUKK!!! <br>";
//     $i++;
// }while($i < 15)

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <table border="1" cellpadding="10" cellspacing="0">
        <?php
        for($i = 1; $i <= 3; $i++){
            echo"<tr>";
            for($b = 1; $b <= 5; $b++){
                echo"<td>$i,$b<td>";
            }
            echo"</tr>";
        }
        ?>
    </table>
</body>
</html>