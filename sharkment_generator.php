<?php

    define("OUTPUT_DIR", __DIR__ . "\\output");

    // Target to male folder
    $inputDir = __DIR__ . '\\input';
    $startViewId = 2500;
    $jobFile = "job.txt";

    if (!file_exists($inputDir)) {
        exit("Not found input dir (" . $inputDir . ")");
    }
    if (!file_exists($jobFile)) {
        exit("Not found job file (" . $jobFile . ")");
    }

    deleteDirectory(OUTPUT_DIR);

    $outputDir = OUTPUT_DIR . "\\sprite\\·Îºê";
    mkdir($outputDir, 0777, true);

    $parentDirMap = [];
    $jobList = file($jobFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $genderList = ["³²", "¿©"];

    $actFiles = glob($inputDir . '/*.{act}', GLOB_BRACE);
    foreach ($actFiles as $actFilePath) {
        $sprFileBaseName = pathinfo($actFilePath, PATHINFO_FILENAME);
        $actFileBaseName = basename($actFilePath);

        // Validate Match File
        $targetSprFilePath = $inputDir . "\\" . $sprFileBaseName . ".spr";
        if (!file_exists($targetSprFilePath)) {
            exit("Not found spr file path (". $targetSprFilePath . ")");
        }

        $parentDirName = removeGenderString($sprFileBaseName);

        if (!isset($parentDirMap[$parentDirName])) {
            $inputFiles = [];
            array_push($inputFiles, str_replace("/", "\\", $actFilePath), $targetSprFilePath);
            $parentDirMap[$parentDirName] = $inputFiles;
        }
    }

    $spriteRobeName = "";
    $spriteRobeId = "";
    $transparenItem = "";

    foreach ($parentDirMap as $parentDirName => $inputFiles) {
        $spriteRobeName .= "	[SPRITE_ROBE_IDs.ROBE_" . $parentDirName . "] = \"" . $parentDirName . "\",\n";
        $spriteRobeId .= "	ROBE_". $parentDirName . " = ". $startViewId . ",\n";
        $transparenItem .= "	{ " . $startViewId++ . ", 255, 255, 25500 },\n";

        $targetDir = $outputDir . "\\" . $parentDirName;
        foreach ($genderList as $gender) {
            mkdir($targetDir . "\\" . $gender, 0777, true);
        }

        foreach ($inputFiles as $inputFile) {
            foreach ($jobList as $job) {
                $fileExtension = pathinfo($inputFile, PATHINFO_EXTENSION);
                foreach ($genderList as $gender) {
                    copy($inputFile, $targetDir . "\\" . $gender . "\\" . str_replace("{{gender}}", $gender, $job) . "." . $fileExtension);
                }
            }
        }
    }

    createTextFile("SpriteRobeName.txt", $spriteRobeName);
    createTextFile("SpriteRobeId.txt", $spriteRobeId);
    createTextFile("transparentitem.txt", $transparenItem);

    function removeGenderString($str) {
        $str = str_replace("³²_", "", $str);
        $str = str_replace("¿©_", "", $str);
        $str = str_replace("남", "", $str);
        $str = str_replace("여", "", $str);

        return $str;
    }

    function deleteDirectory($dir) {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }

        }

        return rmdir($dir);
    }

    function createTextFile($fileName, $content) {
        $file_handle = fopen(OUTPUT_DIR . "\\" . $fileName, 'w');
        fwrite($file_handle, $content);
        fclose($file_handle);
    }

    // $nameList = [];
    // $actFiles = glob("D:\\KRO\\data\\#SPRITE TEXTURE\\sprite\\·Îºê\\c_giant_shark\\¿©" . '/*.{act}', GLOB_BRACE);
    // foreach ($actFiles as $actFilePath) {
    //    // $key = str_replace("_¿©", "", basename($actFilePath));
    //     $key = pathinfo($actFilePath, PATHINFO_FILENAME);
    //     if (!isset($nameList[$key])) {
    //         array_push($nameList, $key);
    //     }
    // }

    // $filename = 'example.txt';

    // $file = fopen($filename, 'w');
    // if ($file) {
    //     foreach ($nameList as $line) {
    //         fwrite($file, str_replace("_¿©", "_{{gender}}", $line) . "\n");
    //     }

    //     fclose($file);
    //     echo "File '$filename' created and content written successfully.";
    // } else {
    //     echo "Error creating or opening the file.";
    // }

?>