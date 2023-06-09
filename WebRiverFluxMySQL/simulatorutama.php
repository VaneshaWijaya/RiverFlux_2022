<!DOCTYPE html>
<html>
<head>
    <title>Riversimulator</title>
    <meta http-equiv="refresh" content="10">
</head>
<body>
    <link rel="stylesheet" type="text/css" href="utama.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>

<?php
include "alamatsql.php";
 
$sqlsungai="SELECT * FROM terpantau";
if ($count = mysqli_query($conn, $sqlsungai)) {
    $rowcount = mysqli_num_rows( $count );
    //printf("Total rows in this table :  %d\n", $rowcount); // Display result
}

$sqlterpantau = "SELECT sungai.ID,sungai.NamaSungai,flux.waktu,flux.tinggi_air,terpantau.Area,terpantau.TinggiNormal, flux.kecepatan,terpantau.jarak
FROM ((flux
INNER JOIN terpantau ON flux.IDpemantau = terpantau.IDpemantau )
INNER JOIN sungai ON sungai.ID = terpantau.ID)
ORDER BY flux.waktu DESC, terpantau.Area
LIMIT 10";
$result = mysqli_query($conn, $sqlterpantau);

$sqlarea ="SELECT sungai.ID,sungai.NamaSungai,terpantau.IDpemantau, terpantau.Area, terpantau.TinggiNormal, flux.waktu,flux.tinggi_air, terpantau.jarak
FROM ((sungai
INNER JOIN terpantau ON sungai.ID = terpantau.ID)
INNER JOIN flux ON terpantau.IDpemantau = flux.IDpemantau)
ORDER BY flux.waktu DESC, terpantau.Area
LIMIT 1";
$resultarea = mysqli_query($conn,$sqlarea);

#untuk bentuk plot/chart
$plotsql= "SELECT sungai.NamaSungai,flux.waktu,flux.tinggi_air
FROM ((flux
INNER JOIN terpantau ON flux.IDpemantau = terpantau.IDpemantau )
INNER JOIN sungai ON sungai.ID = terpantau.ID)
ORDER BY flux.waktu DESC,NamaSungai";
$resultplot = mysqli_query($conn,$plotsql);



$head = fopen("head.html", "r") or die("Unable to open file!");
$foot = fopen("footer.html", "r") or die("Unable to open file!");

echo fread($head,filesize("head.html"));

if (mysqli_num_rows($result)>0){
mysqli_close($conn);


?>

<div class="isi">
    <div class="infowaktu">
        <p> <?php echo date('j/m/Y H:i'); ?> </p>
    </div>

    <div class="info">
        <div class="infoarea">
            <p>Bali</p>
            <a href=""><img src="Image/berawan.png"></a>
            <div class="timeinfo">
                <p>Saturday, 1 June 2021</p>
            </div>
        </div>
        <div class="infoarea">
            <p>Medan</p>
            <a href=""><img src="Image/hujan.png"></a>
            <div class="timeinfo">
                <p>Saturday, 1 June 2021</p>
            </div>
        </div>
        <div class="infoarea">
            <p>Jakarta</p>
            <a href=""><img src="Image/hujan.png"></a>
            <div class="timeinfo">
                <p>Saturday, 1 June 2021</p>
            </div>
        </div>
        <div class="infoarea">
            <p>Palembang</p>
            <a href=""><img src="Image/mendung.png"></a>
            <div class="timeinfo">
                <p>Saturday, 1 June 2021</p>
            </div>
        </div>
        <div class="infoarea">
            <p>Surabaya</p>
            <a href=""><img src="Image/berawan.png"></a>
            <div class="timeinfo">
                <p>Saturday, 1 June 2021</p>
            </div>
        </div>
    </div>

    <div class="judperingatan">
        <p>peringatan banjir</p>
    </div>
        <div class="infoperingatan">
        
        <?php
        while ($row = mysqli_fetch_assoc($resultarea)){
            $tingginormalcm = $row["TinggiNormal"]*100; //dalam cm
            $tinggiair = $row["tinggi_air"]; //dalam cm
            $prediksi = ($tinggiair-$tingginormalcm)/100; //dalam m
            $persen = ($prediksi/2)*100;

            $jarak75 =((75/100)*($row["jarak"]/100)); //dalam m

            if($row["ID"] == 1){
                $websungai = "simulatorCiliwung.php";
            }
            if($row["ID"] == 2){
                $websungai = "Cisadane.php";
            }
            if($row["ID"] == 3){
                $websungai = "Rawakutu.php";
            }

            if ($prediksi >= $jarak75){
                $sinyal="border-color:red";
                $write="color:red";
            } else {
                $sinyal = "border-color:#5f62bc";
                $write="color:#5f62bc";
            }
        ?>        
            <div class="infobanjir" style=<?php echo $sinyal ?>>
                <div class="judarea">
                <p style= <?php echo $write ?> > <a href=<?php echo $websungai;?>><?php echo $row["Area"];?></a></p>
                </div>
                <div class="ketarea">
                <p>Sungai <?php echo $row["NamaSungai"];?></p>
                <p>Persentase Banjir : <?php echo $persen ?> %</p>
                </div>
            </div>
        <?php
        }
        ?>
        </div>
    

    <div class="judperingatan">
        <p>kondisi area terpantau</p>
    </div>
    <table class="desaintable">
        <tr>
            <th>Nama Sungai</th>
            <th>Waktu</th>
            <th>Ketinggian Air</th>
            <th>Kecepatan Aliran </th>
            <th>Status</th>
        </tr>

        <?php
        while($row = mysqli_fetch_assoc($result)){
            $tingginormalcm = $row["TinggiNormal"]*100;
            $tinggiair = $row["tinggi_air"]; //dalam cm
            $prediksi = ($tinggiair-$tingginormalcm)/100; //dalam m
            
            $jarak50 =((50/100)*($row["jarak"]/100));
            $jarak75 =((75/100)*($row["jarak"]/100));

            if ($prediksi >= $jarak50){
            if ($prediksi <= $jarak75){
	            $kondisi = "Siaga";
                $sinyal ="color:orange";
            }else{
	            $kondisi = "Waspada";
                $sinyal="color:red";
            } 
            } else {
	            $kondisi = "Normal";
                $sinyal = "color:green";
            }
        
            if($row["ID"] == 1){
                $websungai = "simulatorCiliwung.php";
            }
            if($row["ID"] == 2){
                $websungai = "Cisadane.php";
            }
            if($row["ID"] == 3){
                $websungai = "Rawakutu.php";
            }
        ?>

        <tr>
            <td><a href= <?php echo $websungai;?>> <?php echo $row["Area"];?></a></td>
            <td><?php echo $row["waktu"];?></td>
            <td><?php echo ($tinggiair/100);?> m </td>
            <td><?php echo ($row["kecepatan"]);?> cm/s </td>
            <td style=<?php echo $sinyal ?> > <?php echo $kondisi;?></td>
        </tr>


    <?php 
     }
    } else {
    echo "0 results";
    }
    ?>
    </table>
</div>

<?php
echo fread($foot,filesize("footer.html"));
?>

</body>
</html>