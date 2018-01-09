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
            //$lines = explode("\n", $str);
            $lines = explode(PHP_EOL, $str);
        }
        catch (Exception $e) {
            echo $e;
        }

        $GSID = "";
        $ResourceURI = "";

        //$hed_string = explode(PHP_EOL, $lines);
        $lineCount =0;
        foreach ($lines as $line) {
            if ($lineCount == 0) {
                $hed_string = explode("\t", $lines[0]);
            }
            $lineCount++;
            if ($lineCount > 0)
            {
                break;
            }
        }

        foreach ($hed_string as $val) {
            echo "hed_string: ".$val."\n";
        }

        $lineCount =0;
        foreach ($lines as $line) {
            if ($lineCount > 0) {
                try {                
                    $DataString = "";
                    $datas = explode("\t", $line);

                    if (count($datas) > 1 ) { //&& strlen($datas[1]) > 0) {
                    
                        // GRGene_ID
                        if ($hed_string[0] == $hed[0]) {
                            $DataString .= "grg:Dataset\tgrg:has_resource\tgrg:".urlencode($datas[0]).".\n";
                            $DataString .= "grg:".urlencode($datas[0])."\tdcterms:identifier\t\"".$datas[0]."\".\n";
                            $DataString .= "grg:".urlencode($datas[0])."\trdf:type\tup:Gene.\n";
                            $DataString .= "grg:".urlencode($datas[0])."\trdfs:label\t\"".$datas[0]." of Glycan related Gene(GRGene)\".\n";
                        }
                        // HGNC ID
                        if ($hed_string[1] == $hed[1]) {
                            $DataString .= "grg:".urlencode($datas[0])."\trdfs:seeAlso\t<http://identifiers.org/hgnc/".urlencode($datas[1])."> .\n";
                        }
                        // Approved Symbol
                        if ($hed_string[2] == $hed[2]) {
                            $DataString .= "grg:".urlencode($datas[0])."\tgrg:approvedSymbol\tgrg:".$datas[2]." .\n";
                            $DataString .= "grg:".urlencode($datas[2])."\trdfs:type\tgrg:ApprovedSymbol .\n";
                            $DataString .= "grg:".urlencode($datas[2])."\trdfs:label\t\"".$datas[2]."\".\n";
                        }
                        // Approved Name
                        if ($hed_string[3] == $hed[3]) {
                            $DataString .= "grg:".urlencode($datas[0])."\tgrg:approvedName\t\"".$datas[3]."\".\n";
                        }
                        // Type (is_a)
                        if ($hed_string[4] == $hed[4]) {
                            if (strlen($datas[4]) > 0) {
                                $DataString .= "grg:".urlencode($datas[0])."\tgrg:is_a\tgrg:".urlencode($datas[4])." .\n";
                            }
                        }
                        // Previous Symbols
                        if ($hed_string[5] == $hed[5]) {
                            if (strlen($datas[5]) > 0 ) {
                                $symbols = explode(",", $datas[5]);
                                foreach ($symbols as $syn){
                                    $DataString .= "grg:".urlencode($datas[0])."\tgrg:previousSymbol\t\"".trim($syn)."\".\n";
                                }
                            }
                        }
                        // Synonyms
                        if ($hed_string[6] == $hed[6]) {
                            if (strlen($datas[6]) > 0 ) {
                                $synos = explode(",", $datas[6]);
                                foreach ($synos as $sy){
                                    $DataString .= "grg:".urlencode($datas[0])."\tgrg:synonym\t\"".trim($sy)."\".\n";
                                }
                            }
                        }
                        // Gene Family Tag
                        if ($hed_string[7] == $hed[7]) {
                            if (strlen($datas[7]) > 0 ) {
                                $tags = explode(",", $datas[7]);
                                foreach ($tags as $tag){
                                    $DataString .= "grg:".urlencode($datas[0])."\tgrg:geneFamilyTag\t\"".trim($tag)."\".\n";
                                }
                            }
                        }
                        // Gene family description
                        if ($hed_string[8] == $hed[8]) {
                            if (strlen($datas[8]) > 0 ) {
                                $DataString .= "grg:".urlencode($datas[0])."\tgrg:geneFamilyDescription\t\"".$datas[8]."\" .\n";
                            }
                        }
                        // $hed[9] = "Chromosome";
                        if ($hed_string[9] == $hed[9]) {
                            if (strlen($datas[9]) > 0 ) {
                                $DataString .= "grg:".urlencode($datas[0])."\tgrg:chromosome\t\"".$datas[9]."\".\n";
                            }
                        }
                        // $hed[10] = "Accession Numbers";
                        if ($hed_string[10] == $hed[10]) {
                            if (strlen($datas[10]) > 0 ) {
                                $acs = explode(",", $datas[10]);
                                foreach ($acs as $ac){
                                    $DataString .= "grg:".urlencode($datas[0])."\tgrg:accessionNumber\t\"".trim($ac)."\".\n";
                                }
                            }
                        }
                        // $hed[11] = "Enzyme IDs";
                        if ($hed_string[11] == $hed[11]) {
                            if (strlen($datas[11]) > 0 ) {
                                $eids = explode(",", $datas[11]);
                                foreach ($eids as $eid){
                                    $DataString .= "grg:".urlencode($datas[0])."\tgrg:enzymeID\t\"".trim($eid)."\".\n";
                                }
                            }
                        }
                        // $hed[12] = "Entrez Gene ID";
                        if ($hed_string[12] == $hed[12]) {
                            if (strlen($datas[12]) > 0 ) {
                                $enids = explode(",", $datas[12]);
                                foreach ($enids as $enid){
                                    $DataString .= "grg:".urlencode($datas[0])."\tgrg:entrezGeneID\t\"".trim($enid)."\".\n";

                                    // http://identifiers.org/ncbigene/
                                    $DataString .= "grg:".urlencode($datas[0])."\trdfs:seeAlso\t<http://identifiers.org/ncbigene/".urlencode(trim($enid))."> .\n";
                                }
                            }
                        }
                        // $hed[13] = "Pubmed IDs";
                        if ($hed_string[13] == $hed[13]) {
                            if (strlen($datas[13]) > 0 ) {
                                $pmids = explode(",", $datas[13]);
                                foreach ($pmids as $pmid){
                                    $DataString .= "grg:".urlencode($datas[0])."\tdcterms:references\t<http://rdf.ncbi.nlm.nih.gov/pubmed/".urlencode(trim($pmid))."> .\n";
                                    $DataString .= "<http://rdf.ncbi.nlm.nih.gov/pubmed/".urlencode(trim($pmid)).">\tdcterms:identifier\t\"".trim($pmid)."\".\n";
                                }
                            }
                        }
                        // $hed[14] = "RefSeq IDs";

                            // skip

                        // $hed[15] = "UniProt ID(supplied by UniProt)";
                        if ($hed_string[15] == $hed[15]) {
                            if (strlen($datas[15]) > 0 ) {
                                $upids = explode(",", $datas[15]);
                                foreach ($upids as $upid){
                                    //$DataString .= "grg:".$datas[0]."\tgrg:uniProtID\t\"".trim($upid)."\".\n";
                                    $DataString .= "grg:".urlencode($datas[0])."\trdfs:seeAlso\t<http://purl.uniprot.org/uniprot/".urlencode(trim($upid))."> .\n";
                                    $DataString .= "grg:".urlencode($datas[0])."\trdfs:seeAlso\t<http://identifiers.org/uniprot/".urlencode(trim($upid))."> .\n";

                                    // rdf:type up:Protein ;
                                    $DataString .= "<http://identifiers.org/uniprot/".urlencode(trim($upid)).">\trdf:type\tup:Protein .\n";
                                    $DataString .= "<http://identifiers.org/uniprot/".urlencode(trim($upid)).">\tdcterms:identifier\t\"".trim($upid)."\".\n";
                                }
                            }
                        }
                        //$hed[16] = "taxonomy ID";
                        if ($hed_string[16] == $hed[16]) {
                            if (strlen($datas[16]) > 0 ){
                                $DataString .= "<http://identifiers.org/taxonomy/".urlencode($datas[16]).">\tdcterms:identifier\t\"".$datas[16]."\".\n";

                                $DataString .= "grg:".urlencode($datas[0])."\trdfs:seeAlso\t<http://identifiers.org/taxonomy/".urlencode($datas[16])."> .\n";
                                $DataString .= "<http://identifiers.org/taxonomy/".urlencode($datas[16]).">\trdfs:Type\tup:Taxon .\n";
                            }
                        }

                        // $hed[17] = "taxonomy name";
                        if ($hed_string[17] == $hed[17]) {
                            if (strlen($datas[17]) > 0 ){
                                $DataString .= "<http://identifiers.org/taxonomy/".urlencode($datas[16]).">\trdfs:label\t\"".$datas[17]."\".\n";

                                $DataString .= "grg:".urlencode($datas[0])."\trdfs:seeAlso\t<http://identifiers.org/taxonomy/".urlencode($datas[16])."> .\n";
                            }
                        }


                        echo $DataString;
                        file_put_contents($savepath, $DataString, FILE_APPEND | LOCK_EX); //追記モード
                    }

                }
                catch (Exception $e) {
                    echo "ERROR: ".$line."/n";
                }
            }
            $lineCount++;
        }
        file_put_contents($savepath, "\n", FILE_APPEND | LOCK_EX); //追記モード
    }
    echo "wrote $file...\n".htmlspecialchars($file)."\n";
}
echo "fin...";
?>  