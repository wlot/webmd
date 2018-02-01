<?php
class Html()
{
  private $http_path = "/wiki";
  public function __construct()
  {
  }

  public function get_html($links, $body, $body_attr = "")
  {
    $html = '<html><head>
<meta name="viewport" content="width=device-width, initial-scale=1" />';
    foreach ($links as &$link) {
      $html .= '<link rel="stylesheet" href="'.$http_path.'/css/'.$link.'.css />';
    }
    $html .= '</head><body '.$body_attr.'>'.$body.'</body></html>';
    return $html;
  }
}
?>
