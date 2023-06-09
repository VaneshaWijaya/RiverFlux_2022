<!DOCTYPE html>
<html>
    <head>
        <title>RiverFlux | Sungai Ciliwung </title>
        <meta http-equiv="refresh" content="10">
    </head>

    <body>
        <link rel="stylesheet" type="text/css" href="websungai.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
        <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
        
        <?php
        include "alamatsql.php";
        $sqldata = "SELECT tinggi_air FROM flux ORDER BY waktu DESC";
        $getdata = mysqli_query($conn, $sqldata);

        $ID = 1;

        $sqlsungai ="SELECT * FROM terpantau WHERE ID = $ID";
        if ($count = mysqli_query($conn, $sqlsungai)) {
            $rowcount = mysqli_num_rows( $count );
            //printf("Total rows in this table :  %d\n", $rowcount);// Display result
        }

        while($row = mysqli_fetch_assoc($getdata)){
            $tes = $row['tinggi_air'];
            #$do = print_r($tes.',');
        }
        
        $sqlciliwung = "SELECT sungai.ID,sungai.NamaSungai,flux.waktu, flux.tinggi_air,terpantau.TinggiNormal, flux.kecepatan, terpantau.Area, terpantau.jarak
        FROM ((flux
        INNER JOIN terpantau ON flux.IDpemantau = terpantau.IDpemantau )
        INNER JOIN sungai ON sungai.ID = terpantau.ID)
        WHERE sungai.ID='$ID'
        ORDER BY flux.waktu DESC,terpantau.Area
        LIMIT 10";
        $result = mysqli_query($conn, $sqlciliwung);

        $sqljarak ="SELECT terpantau.ID, terpantau.IDpemantau, terpantau.Area, terpantau.TinggiNormal,flux.waktu, flux.tinggi_air,flux.kecepatan, terpantau.jarak
        FROM (flux INNER JOIN terpantau ON flux.IDpemantau = terpantau.IDpemantau )
        WHERE terpantau.ID = '$ID'
        ORDER BY flux.waktu DESC, terpantau.Area
        LIMIT $rowcount";
        $resultjarak = mysqli_query($conn,$sqljarak);

        $head = fopen("head.html", "r") or die("Unable to open file!");
        $foot = fopen("footer.html", "r") or die("Unable to open file!");

        echo fread($head,filesize("head.html"));


        if (mysqli_num_rows($result)>0){
        mysqli_close($conn);
        ?>

        <div class="isi">
            <div class="info">       
                <h1>Sungai Ciliwung </h1>
                <div class="infoarea">
                    <div class="keterangan">
                        <a href="https://goo.gl/maps/4txBREDdJdz48mPs9"><img src="Image/iCiliwung.jpg"></a>
                        <div class="bottomright">
                            <p>sumber gambar : </p>
                            <p>www.cnbcindonesia.com</p>
                        </div>
                        <div class="alamatsungai">
                            <ol>klik untuk buka map</ol>
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3967.068998239799!2d106.82707445042385!3d-6.12141569554591!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6a1e0fd4178039%3A0x88c80fe17512848!2sCiliwung!5e0!3m2!1sen!2sid!4v1650890772914!5m2!1sen!2sid" width="120" height="100" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                
                        </div>
                    </div>
                    <div class="keterangan">
                        <p> [Informasi ringkas terkait sungai]</p>
                        <ul>Tentang Sungai
                            <li>Panjang : </li>
                            <li>Luas : </li>
                            <li>Kedalaman : </li>
                        </ul>
                    </div>
                </div>


                <h2>Kondisi Sungai</h2>
                <table class="tablekondisi">
                    <tr>
                         <th>Area Pemantauan</th>
                         <th>Jarak Permukaan | <br> Sungai - Daratan</th>
                         <th>Kecepatan Aliran</th>
                         <th>Kondisi Sungai</th>
                    </tr>

                    <?php while($row = mysqli_fetch_assoc($resultjarak)){
                        $tingginormalcm = $row["TinggiNormal"]*100;
                        $tinggiair = $row["tinggi_air"]; //dalam cm
                        $penambahan = $tinggiair-$tingginormalcm ; //dalam cm
                        $jarak = $row["jarak"] - $penambahan; //dalam cm
                        $prediksi = $penambahan/100; //dalam m
                        $kecepatan = $row["kecepatan"];
                        
                        $jarak50 =((50/100)*($row["jarak"]/100));
                        $jarak75 =((75/100)*($row["jarak"]/100));


                        if ($prediksi >= $jarak50){
                            if ($prediksi <= $jarak75){
                                $kodep = 2;
                            }else{
                                $kodep = 3;
                            } 
                        }else {
                            $kodep = 1;
                        }

                        if($kecepatan >=25){ 
                            if($kecepatan <=50){
                                $kodek = 2;
                            }else{
                                $kodek = 3;
                            }
                        }else {
                            $kodek = 1;
                        }

                        #new, for condition image | #10-25 pelan, 25-50 normal, 50-100 cepat
                        if($kodep == 1){ 
                            if($kodek == 1){
                                $image = "Image/normalp.png";
                            }
                            else if($kodek == 2){
                                $image = "Image/normal.png";
                            }else{
                                $image = "Image/normalc.png";
                            }

                        }else if($kodep == 2) {
                            if($kodek == 1){
                                $image = "Image/siagap.png";
                            }
                            else if($kodek == 2){
                                $image = "Image/siaga.png";
                            }else{
                                $image = "Image/siagac.png";
                            }
                        
                        }else if($kodep == 3){
                            if($kodek == 1){
                                $image = "Image/waspadap.png";
                            }
                            else if($kodek == 2){
                                $image = "Image/waspada.png";

                            }else{
                                $image = "Image/waspadac.png";
                            }
                        }
                     ?>

                    <tr>
                        <th> <?php echo $row["Area"];?> </th>
                        <td> <?php echo ($jarak/100);?> m</td>
                        <td> <?php echo $row["kecepatan"];?> cm/s</th>
                        <td> <div class="imgtd"> <img src= <?php echo $image ?> > </div></td>
                    </tr>
                    <?php
                    } 
                    ?>

                </table>
                
                <h2>Riwayat Pemantauan</h2>
                <table class="desaintable">
                    <tr>
                        <th>Nama Sungai</th>
                        <th>Waktu</th>
                        <th>Ketinggian Air</th>
                        <th>Kecepatan Aliran</th>
                        <th>Status</th>
                    </tr>

                    <?php while($row = mysqli_fetch_assoc($result)){
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
                    ?>

                    <tr>
                        <td><?php echo $row["Area"];?></td>
                        <td><?php echo $row["waktu"];?></td>
                        <td><?php echo ($tinggiair/100);?> m </td>
                        <td><?php echo $row["kecepatan"]; ?> cm/s </td>
                        <td style=<?php echo $sinyal ?> > <?php echo $kondisi;?></td>
                    </tr>
                    <?php
                    } }
                    else{
                        echo "0 result";
                    }
                    ?>
                </table>

                <h2>Grafik Sungai</h2>
                <div class="infografik">
                    
                </div>

                <h2>Referensi</h2>
                <div class="referensi">
                    <ol>
                    <li>...</li>
                    <li>...</li>
                    </ol>
                </div>
            </div>
        </div>

        <?php
        echo fread($foot,filesize("footer.html"));
        ?>
    </body>
</html>

<script>
    <?php 
    while($row = mysqli_fetch_assoc($getdata)){
        $Arrayitem = array ();
        $ArrayItem['label'] = $row['waktu'];
        $Arrayitem['value'] = $row['tinggi_air'];
        $tes = $row['tinggi_air'];
        print_r($tes);

    ?>

    var xArray = [ <?php $Arrayitem['label']; ?> ];
    var yArray = [ <?php $Arrayitem['value']; ?> ];

    <?php
    }
    ?>
    
    // Define Data
    var data = [{
        x: xArray,
        y: yArray,
        mode: "lines"
    }];

    // Define Layout
    var layout = {
        xaxis: { range: [1,24], title: "Time [Jam]" },
        yaxis: { range: [1,30], title: "Meters" },
        title: "Perubahan Ketinggian Sungai"
    };

    // Display using Plotly
    Plotly.newPlot("myPlot", data, layout);
</script>