static_converterクラス
================

//静的なファイル(JS, CSSなど)のソース内部にあるURLパスをBASE_URLに合わせて置換するクラス
// 例) ドメインが「http://localhost/ebbey」の場合
$this->load->library('Static_converter', array('replace_str' => '/ebbey'));

// 一番最初のページのみパスを置換
$this->static_converter->getdirlist(CSS_BASE_PATH, TRUE);
