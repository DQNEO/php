#!/usr/bin/php
<?php
/**
 * Hyper Pudding
 * - yet another rapid and stupid search engine for PHP source code. -
 *
 * @author  DQNEO (forked from sotarok)
 * @see     http://www.slideshare.net/sotarok/php-source-code-search-with-php
 * @versoin 0.0.1
 * @license The MIT License
 * @require  PHP > 5.1
 *
 * usage:
 *   - step 1: make index
 *   $ php pudding.php make dir1 dir2 dir3 ...
 *
 *   - step 2: search
 *   $ php pudding.php search anykeyword
 *
 */
ini_set("memory_limit", -1);

class Pudding_Index {

    private $base_dir = '/tmp/pudding/';
    private $tmp_inverted_index = array();

    /**
     *  インデックスを削除する
     */
    public function cleanup()
    {
        if (! is_dir($this->base_dir)) {
            mkdir($this->base_dir, 0777);
            return ;
        }

        foreach (glob($this->base_dir . '/*') as $file) {
            unlink($file);
        }

    }

    /**
     *  インデックスにエントリを追加する
     *
     *  @param  string $keyword 
     *  @param  array $item
     */
    public function add($keyword, array $item)
    {
        $this->tmp_inverted_index[$keyword][] = $item;
    }

    /**
     *  インデックスをメモリからディスクに保存する
     */
    public function save()
    {
        foreach ($this->tmp_inverted_index as $keyword => $content) {
            file_put_contents($this->filename($keyword), serialize($content));
        }
    }

    /**
     *  インデックスファイルのファイル名を返す
     *
     *  @param  string $keyword
     *  @return string $filename 絶対パス
     */
    private function filename($keyword)
    {
        return $this->base_dir . md5($keyword);
    }

    /**
     *  indexにエントリが存在するかどうかを調べる
     *
     *  @return bool
     */
    public  function exist($keyword)
    {
        return is_file($this->filename($keyword));
    }

    /**
     *  indexからエントリを取得する
     *
     *  @return array $entry
     */
    private function get($keyword)
    {
        return unserialize(file_get_contents($this->filename($keyword)));
    }

    /**
     *  indexからエントリを取得し、スコアリングして返す
     *
     *  @return array $scored
     */
    public function score($keyword)
    {
        return $this->_score($this->get($keyword));
    }

    /**
     *  スコアリングする(未実装)
     *
     *  @return array $scored
     */
    private function _score(&$ii)
    {
        $scored = array();
        $tmp = array();
        foreach ($ii as $k => $v) {
            if (isset($scored[$v[0]])) {
                $scored[$v[0]]['count']++;
                $scored[$v[0]]['pos'][] = $v[1];
            }
            else {
                $scored[$v[0]] = array(
                    'count' => 0,
                    'pos' => array($v[1]),
                    );
            }
        }

        /* 
           uasort($scored, function($v1, $v2) {
           return $v1['count'] < $v2['count'];
           });
        */
        return $scored;
    }

}

class Pudding_Builder
{
    public $verbose = true;

    protected $_target_dirs = array();

    protected $_files = array();

    protected $_target_tokens = array(
        T_STRING_CAST,
        T_STRING_VARNAME,
        T_STRING,
        T_VARIABLE,
        );

    private $index;

    public function __construct($dirs)
    {
        $this->index = new Pudding_Index();
        $this->parseArgv($dirs);
    }

    private function parseArgv($dirs)
    {
        for($i = 1; $i < count($dirs); $i++) {
            $this->_target_dirs[] = rtrim($dirs[$i], "/");
        }
    }

    /**
     *  インデックスを構築する
     *
     */
    public function make()
    {
        $sec =  microtime(true);

        $this->index->cleanup();

        foreach ($this->_target_dirs as $dir) {
            $this->find_files($dir);
        }

        $this->parseFiles();
        $esec =  microtime(true);
        $this->info("Index Built on Memory: ", $esec - $sec ," sec", PHP_EOL);
        $this->report_memory();

        $this->index->save();

        $this->info("Index Saved: ", $esec - $sec ," sec", PHP_EOL) ;
    }

    /**
     *  ディレクトリを再帰的にたどってファイルリストを作成する
     *
     *  @param string $dirname
     */
    private function find_files($dirname)
    {
        foreach (glob($dirname . "/*") as $node) {
            if (is_dir($node)) {
                $dir = $node;
                $this->find_files($dir);
            } else {
                $file = $node;
                if (preg_match('/.+\.(' . 'php' .')$/', $file)) {
                    $this->_files[] = $file;
                }
            }
        }

    }

    private function parseFiles()
    {
        foreach ($this->_files as $file) {

            $this->parseFile($file);
        }
    }

    /**
     *  PHPファイルをパースする
     *
     *  @param string $file パス名
     */
    private function parseFile($file)
    {
        foreach (token_get_all(file_get_contents($file)) as $token) {
            if (! is_array($token)) {
                continue;
            }

            list($token_const_id, $keyword, $lineno) = $token;

            if (in_array($token_const_id, $this->_target_tokens)) {
                $this->index->add($keyword, array($file, $lineno));
            }
        }
    }

    public function info()
    {
        if ($this->verbose) {
            $args = func_get_args();
            echo join(" ", $args);
        }
    }

    public function report_memory ()
    {
        $msg = sprintf("mem: %.5f MB used.\n" , memory_get_usage()/1024/1024 );
        $this->info($msg);
    }
}

class Pudding_Searcher
{
    private $index;

    public function __construct()
    {
        $this->index = new Pudding_Index();
    }

    /**
     *  検索をする
     *
     *  @param string $keyword
     */
    public function search($keyword)
    {
        $sec = microtime(true);
        $res = $this->_search($keyword);

        echo "$keyword :", PHP_EOL;
        if (! $res) {
            echo "Not Found.", PHP_EOL;
            exit(0);
        }

        foreach ($res as $k => $r) {
            echo sprintf("     %-50s line %s", $k, join(", ", $r['pos'])), PHP_EOL;
             }

        $esec = microtime(true);
        echo "sec: ", printf("%.5f", $esec - $sec), PHP_EOL;
        echo PHP_EOL;
    }

    /**
     *  検索をする(内部処理)
     *
     *  @param string $keyword
     *  @return score or false ヒットなし
     */
    private function _search($keyword)
    {
        if (! $this->index->exist($keyword)) {
            return false;
        }

        return $this->index->score($keyword);
    }

}

class Pudding {

    public static function run($argc, $argv)
    {
        $cmd_name = basename(__FILE__);
        if ($argc < 3) {
            fprintf(STDERR, "
Invalid arguments:
  usege:
    php $cmd_name make(or m) dir [, dir2, dir3 ,..]
    php $cmd_name search(or s) keyword
\n");
            exit(1);
        }

        if (preg_match('/^(m|make)$/', $argv[1])) {
            array_shift($argv);
            $builder = new Pudding_Builder($argv);
            $builder->make();
            exit(0);
        } else if (preg_match('/^(s|search)$/', $argv[1])) {
            $keyword = $argv[2];
            $searcher = new Pudding_Searcher();
            $searcher->search($keyword);
        }

    }
}

Pudding::run($argc, $argv);
