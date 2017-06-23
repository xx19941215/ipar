<?php
namespace Gap\Tool;

trait FileCompilerTrait {
    /*
    public function compile($target_path)
    {
        $lines = [];
        $lines[] = '<?php';

        foreach ($this->includes as $include) {
            if ($handle = fopen($include, 'r')) {
                while (($line = fgets($handle)) !== false) {
                    if ((strpos($line, '<?php') === false) &&
                        (strpos($line, '$this->includeFile') === false) &&
                        (strpos($line, '$this->includeDir') === false)
                    ) {
                        $line = rtrim($line);
                        $lines[] = $line;
                    }
                }
                fclose($handle);
            }
        }
        file_put_contents($target_path, implode("\n", $lines));
    }
     */

    public function includeFile($file, $is_required = true) {
        if (file_exists($file)) {
            $this->includes[] = $file;
            $config = $this;
            include $file;
        } else if ($is_required) {
            echo "cannot find file $file";
            exit();
        }
    }
    public function includeDir($dir) {
        $files = scandir($dir);
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) == 'php') {
                $this->includeFile($dir . '/' . $file);
            }
        }
    }

}
