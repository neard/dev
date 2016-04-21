<?php

class DevCheckLang
{
    const DEFAULT_LANG = 'english';
    
    private $neardevBs;
    
    public function __construct(DevBootstrap $neardevBs, $args)
    {
        $this->neardevBs = $neardevBs;
        $this->process();
    }
    
    private function process()
    {
        require_once $this->neardevBs->getClassesPath() . '/class.lang.php';
        
        $defaultFile = file($this->neardevBs->getLangsPath() . '/' . self::DEFAULT_LANG . '.lng');
        $defaultRaw = parse_ini_file($this->neardevBs->getLangsPath() . '/' . self::DEFAULT_LANG . '.lng');
        
        foreach ($this->getLangList() as $lang) {
            if ($lang != self::DEFAULT_LANG) {
                $missing = array();
                $badFormat = array();
                $notTranslated = array();
                $raw = parse_ini_file($this->neardevBs->getLangsPath() . '/' . $lang . '.lng');
                if ($raw !== false) {
                    echo PHP_EOL . '## ' . strtoupper($lang) . PHP_EOL;
                    foreach (Lang::getKeys() as $key) {
                        // Missing
                        if (!isset($raw[$key])) {
                            $missing[$key] = $this->findLineNumber($defaultFile, $key);
                            continue;
                        }
                        
                        // Count format
                        $countFormatDefault = substr_count($defaultRaw[$key], '%s');
                        $countFormat = substr_count($raw[$key], '%s');
                        if ($countFormatDefault != $countFormat) {
                            $badFormat[$key] = $this->findLineNumber($defaultFile, $key);
                        }
                        
                        // Not translated
                        if (DevUtils::startWith($raw[$key], '#')) {
                            $notTranslated[$key] = $this->findLineNumber($defaultFile, $key);
                        }
                    }
                    
                    echo '=> Missing: ';
                    if (!empty($missing)) {
                        echo count($missing) . PHP_EOL;
                        foreach ($missing as $key => $lineNumber) {
                            echo '  ' . $key . ' (line ' . $lineNumber . ')' . PHP_EOL;
                        }
                    } else {
                        echo 'N/A' . PHP_EOL;
                    }
                    
                    echo '=> Bad format: ';
                    if (!empty($badFormat)) {
                        echo count($badFormat) . PHP_EOL;
                        foreach ($badFormat as $key => $lineNumber) {
                            echo '  ' . $key . ' (line ' . $lineNumber . ')' . PHP_EOL;
                        }
                    } else {
                        echo 'N/A' . PHP_EOL;
                    }
                    
                    echo '=> Not translated: ';
                    if (!empty($notTranslated)) {
                        echo count($notTranslated) . PHP_EOL;
                        foreach ($notTranslated as $key => $lineNumber) {
                            echo '  ' . $key . ' (line ' . $lineNumber . ')' . PHP_EOL;
                        }
                    } else {
                        echo 'N/A' . PHP_EOL;
                    }
                }
            }
        }
        
        echo PHP_EOL;
    }
    
    private function getLangList()
    {
        $result = array();
        
        $handle = @opendir($this->neardevBs->getLangsPath());
        if (!$handle) {
            return $result;
        }
        
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != ".." && DevUtils::endWith($file, '.lng')) {
                $result[] = str_replace('.lng', '', $file);
            }
        }
        
        closedir($handle);
        return $result;
    }
    
    private function findLineNumber($fileContent, $key)
    {
        foreach ($fileContent as $lineNumber => $lineContent) {
            $expLineContent = explode('=', $lineContent);
            $row = trim($expLineContent[0]);
            if ($row == $key) {
                return $lineNumber + 1;
            }
        }
        return null;
    }
}
