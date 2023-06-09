<!DOCTYPE html>
<html>
<head>
    <title>RiverFlux</title>
    <meta http-equiv="refresh" content="10">
</head>
<body>
    <link rel="stylesheet" type="text/css" href="utamapostgre.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>

<?php
include "alamatpostgre.php";

$sqlsungai = "SELECT * FROM terpantau";
if($count = pg_query($conn,$sqlsungai)){
    $rowcount = pg_num_rows($count);
    //printf("Total data dalam tabel : %d\n",$rowcount); //Didsplay the result
}

$sqlterpantau = "SELECT sungai.ID,sungai.NamaSungai,flux.waktu,flux.tinggi_air,terpantau.area,terpantau.TinggiNormal,flux.kecepatan,terpantau.jarak
FROM ((flux
INNER JOIN terpantau ON flux.IDpemantau = terpantau.IDpemantau )
INNER JOIN sungai ON sungai.ID = terpantau.ID)
ORDER BY flux.waktu DESC, terpantau.area
LIMIT 10";
$result = pg_query($conn, $sqlterpantau);

$sqlarea ="SELECT sungai.ID,sungai.NamaSungai,terpantau.IDpemantau, terpantau.Area, terpantau.TinggiNormal, flux.waktu,flux.tinggi_air, terpantau.jarak
FROM ((sungai
INNER JOIN terpantau ON sungai.ID = terpantau.ID)
INNER JOIN flux ON terpantau.IDpemantau = flux.IDpemantau)
ORDER BY flux.waktu DESC, terpantau.area
LIMIT $rowcount";
$resultarea = pg_query($conn,$sqlarea);

#untuk bentuk plot/chart
$plotsql= "SELECT sungai.NamaSungai,flux.waktu,flux.tinggi_air
FROM ((flux
INNER JOIN terpantau ON flux.IDpemantau = terpantau.IDpemantau )
INNER JOIN sungai ON sungai.ID = terpantau.ID)
ORDER BY flux.waktu DESC";
$resultplot = pg_query($conn,$plotsql);



$head = fopen("head.html", "r") or die("Unable to open file!");
$foot = fopen("footer.html", "r") or die("Unable to open file!");

echo fread($head,filesize("head.html"));

if (pg_num_rows($result)>0){
pg_close($conn);


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
        while ($row = pg_fetch_object($resultarea)){
            $tingginormalcm = $row->tingginormal*100; //dalam cm
            $penambahan = ($row->tinggi_air) - $tingginormalcm; //dalam cm
            $prediksi = $penambahan/100; //dalam m
            $persen = ($prediksi/2)*100;
            $jarak50 =((50/100)*($row->jarak/100));
            $jarak75 =((75/100)*($row->jarak/100));

            if($row->id == 1){
                $websungai = "cilipostgre.php";
            }
            if($row->id == 2){
                $websungai = "cisapostgre.php";
            }
            if($row->id == 3){
                $websungai = "rawapostgre.php";
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
                <p><a href=<?php echo $websungai;?>><?php echo $row->area;?></a></p>
                </div>
                <div class="ketarea">
                <p>Sungai <?php echo $row->namasungai;?></p>
                <p>Persentase Banjir : <?php echo $persen; ?> %</p>
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
        while($row = pg_fetch_object($result)){
            $tingginormalcm = ($row->tingginormal)*100; //dalam cm
            $tinggiair = $row->tinggi_air; //dalam cm
            $prediksi = ($tinggiair-$tingginormalcm)/100; //dalam m
            $jarak50 =((50/100)*($row->jarak/100));
            $jarak75 =((75/100)*($row->jarak/100));

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
        
            if($row->id == 1){
                $websungai = "cilipostgre.php";
            }
            if($row->id == 2){
                $websungai = "cisapostgre.php";
            }
            if($row->id == 3){
                $websungai = "rawapostgre.php";
            }
        ?>

        <tr>
            <td><a href= <?php echo $websungai;?>> <?php echo $row->area;?></a></td>
            <td><?php echo $row->waktu;?></td>
            <td><?php echo ($tinggiair/100);?> m</td>
            <td><?php echo $row->kecepatan ;?> cm/s </td>
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

<script>
    
    plotvalues =[]
    var xValues = ["Ciliwung", "Cisadane", "Rawakutuk"];
    var yValues = [45, 49, 57];
    var barColors = ["red", "green", "blue"];

    new Chart("myChart", {
        type: "bar",
        data: {
            labels: xValues,
            datasets: [{
                backgroundColor: barColors,
                data: yValues
            }]
        },
        options: {
            legend: { display: false },
            title: {
                display: true,
                text: "Ringkasan yang berlangsung"
            }
        }
    });
</script>