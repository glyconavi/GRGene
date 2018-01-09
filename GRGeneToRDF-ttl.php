<?php
header('Content-Type: text/plain;charset=UTF-8');
ini_set('auto_detect_line_endings', 1);
ini_set('auto_detect_line_endings', 1);
date_default_timezone_set('Asia/Tokyo');
$timeHeader = date("Y-m-d_H-i-s");

//作成したいディレクトリ（のパス）
$directory_path = "RDF";    //この場合、同じ階層に「RDF」というディレクトリを作成する 
//「$directory_path」で指定されたディレクトリが存在するか確認
if(file_exists($directory_path)){
    //存在したときの処理
    echo "作成しようとしたディレクトリは既に存在します";
}else{
    //存在しないときの処理（「$directory_path」で指定されたディレクトリを作成する）
    if(mkdir($directory_path, 0777)){
        //作成したディレクトリのパーミッションを確実に変更
        chmod($directory_path, 0777);
        //作成に成功した時の処理
        echo "作成に成功しました";
    }else{
        //作成に失敗した時の処理
        echo "作成に失敗しました";
    }
}

ここから以下はまだなにもやってない

//$fileHeader = date("Y-m-d");
$savepath = $directory_path.DIRECTORY_SEPARATOR."GlycoNAVI-GRGene_".$timeHeader.".ttl";

$DataString = "@prefix grg: <http://glyconavi.org/glycobio/grgene/> .\n";
$DataString .= "@prefix dcterms: <http://purl.org/dc/terms/> .\n";
$DataString .= "@prefix xsd: <http://www.w3.org/2001/XMLSchema#> .\n";
$DataString .= "@prefix rdfs:  <http://www.w3.org/2000/01/rdf-schema#> .\n";
$DataString .= "@prefix rdf:   <http://www.w3.org/1999/02/22-rdf-syntax-ns#> .\n";
$DataString .= "@prefix fabio: <http://purl.org/spar/fabio/> .\n";
$DataString .= "@prefix up: <http://purl.uniprot.org/core/> .\n";
$DataString .= "@prefix sio: <http://semanticscience.org/resource/> .\n";
// EDAM bioinformatics operations, types of data, data formats, identifiers, and topics
$DataString .= "@prefix edam:  <http://edamontology.org/> .\n"; // 
//$DataString .= "@prefix skos: <http://www.w3.org/2004/02/skos/core#> .\n";
$DataString .= "\n";

file_put_contents($savepath, $DataString, FILE_APPEND | LOCK_EX); //追記モード


$gssamplearray = array();

foreach(glob('./{*.txt}',GLOB_BRACE) as $file) {
    if(is_file($file)) {
        $gssamplearray[] = $file;
    }
}

sort($gssamplearray);

$hed = array();
$hed[0] = "GRG_ID";
$hed[1] = "HGNC ID";
$hed[2] = "Approved Symbol";
$hed[3] = "Approved Name";
$hed[4] = "Type";
$hed[5] = "Previous Symbols";
$hed[6] = "Synonyms";
$hed[7] = "Gene Family Tag";
$hed[8] = "Gene family description";
$hed[9] = "Chromosome";
$hed[10] = "Accession Numbers";
$hed[11] = "Enzyme IDs";
$hed[12] = "Entrez Gene ID";
$hed[13] = "Pubmed IDs";
$hed[14] = "RefSeq IDs";
$hed[15] = "UniProt ID(supplied by UniProt)";
$hed[16] = "taxonomy ID";
$hed[17] = "taxonomy name";


// get file names
foreach($gssamplearray as $file) {
    if(is_file($file)) {
        // get file path
        echo htmlspecialchars($file)."\n";
        // lines
        $lines = array();
        try {
            $filedata = file_get_contents($file);
            $str = str_replace(array("\r\n","\r","\n"), "\n", $filedata);
            $lines = explode("\n", $str);
        }
        catch (Exception $e) {
            echo $e;
        }

        $GSID = "";
        $ResourceURI = "";

        $hed_string = explode("\t", $lines);
        
        foreach ($lines as $line) {
            try {                
                $DataString = "";
                $datas = explode("\t", $line);

                if (count($datas) > 1 ) { //&& strlen($datas[1]) > 0) {
                
                    // GRGene_ID
                    if ($hed_string[0] == $hed[0]) {
                        $DataString .= "grg:Dataset\tgrg:has_resource\tgrg:".urlencode($datas[0]).".\n";
                        $DataString .= "grg:".urlencode($datas[0])."\tdcterms:identifier\t\"".$datas[0]."\".\n";
                        $DataString .= "grg:".urlencode($datas[0])."\trdf:type\tgrg:Gene.\n";
                        $DataString .= "grg:".urlencode($datas[0])."\trdfs:label\t\"".$datas[0]." of GRGene\".\n";
                    }
                    // HGNC ID
                    else if ($hed_string[1] == $hed[1]) {
                        $DataString .= "grg:".urlencode($datas[0])."\trdfs:seeAlso\t<http://identifiers.org/hgnc/".urlencode($datas[1]).">.\n";
                    }
                    // Approved Symbol
                    else if ($hed_string[2] == $hed[2]) {
                        $DataString .= "grg:".urlencode($datas[0])."\tgrg:approvedSymbol\tgrg:".urlencode($datas[2])." .\n";
                        $DataString .= "grg:".urlencode($datas[2])."\trdfs:type\tgrg:ApprovedSymbol .\n";
                        $DataString .= "\tgrg:".urlencode($datas[2])."\trdfs:label\t\"".$datas[2]."\".\n";
                    }









                    echo $DataString;
                    file_put_contents($savepath, $DataString, FILE_APPEND | LOCK_EX); //追記モード
                }

            }
            catch (Exception $e) {
                echo "ERROR: ".$line."/n";
            }
        }
        file_put_contents($savepath, "\n", FILE_APPEND | LOCK_EX); //追記モード
    }
    echo "wrote $file...\n".htmlspecialchars($file)."\n";
}
echo "fin...";
?>  
