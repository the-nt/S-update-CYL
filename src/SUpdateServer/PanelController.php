<?php /** @noinspection ALL */

use Symfony\Component\HttpFoundation\RedirectResponse;

 /**
  * The panel controller to display the panel things
  *
  * @package \SUpdateServer
  * @version 3-(Base-2.0.0-BETA)
  */
class PanelController {

    public function getPanel($request)
    {
        if ($request == "home")
            return SUpdateServer::app()->display("home.twig", array(
                "silexVersion" => SUpdateServer::VERSION,
                "server" => SUpdateServer::SERVER_VERSION,
                "base" => SUpdateServer::BASE_VERSION,
                "panel" => SUpdateServer::PANEL_VERSION,
                "internal" => SUpdateServer::INTERNAL_VERSION,
                "serverEnabled" => SUpdateServer::serverConfig()->get("enabled")
            ));
        else if ($request == "settings") {
            return SUpdateServer::app()->display("settings.twig");
        }

        else if (strstr($request, 'explorer')) {
            $request = str_replace('explorer', '', isset($_GET['file']) ? $_GET['file'] : '');
            $folder = str_replace(':', '/', $request);
            $aurl = explode(':', $request);
            $aurl = array_filter($aurl);
            array_pop($aurl);

            return SUpdateServer::app()->display('files.twig', array(
                'files' => self::listFiles($folder),
                'burl' => implode(':', $aurl),
                'url' => $request . ':',
            ));
        } else if ($request == 'del') {
            $delFile = $_GET['file'];
            $delFile = str_replace('file', './files', $delFile);
            $delFile = str_replace('explorer', './files', $delFile);
            $delFile = str_replace(':', '/', $delFile);
            if (!is_dir($delFile)) {
                unlink($delFile);
            } else {
                self::rrmdir($delFile);
            }
            $url = explode(':', str_replace('file', 'explorer', $_GET['file']));
            array_pop($url);

            return new RedirectResponse(implode(':', $url));
        } else {
            return new RedirectResponse('home');
        }
    }

    /**
     * @param $request
     * @return RedirectResponse
     */
    public function postPanel($request) {
        if($request == "settings"){
            if(isset($_POST["password"])) {
                SUpdateServer::serverConfig()->set("password", sha1($_POST["password"]));
                return new RedirectResponse("home");
            }}
             else if ($request == 'explorer') {
            if (isset($_POST['url']) && isset($_POST['rename'])) {
                $origine = $file = explode(':', str_replace('explorer', './files',  $_POST['url']));
                array_pop($file);
                    rename(implode('/', $origine), implode('/', $file).'/'.$_POST['rename']);
                        return new RedirectResponse('explorer');
    }
            }
            else if ($request == 'upload'){
                print($_POST['location']);
                print($_FILES['file']['name']);
            $dir = './files' . str_replace(':', '/', $_POST['location']);
            $dir = str_replace('..', '.', $dir);
                $dirFile = $dir . str_replace(' ', '_', basename($_FILES['file']['name']));
            move_uploaded_file($_FILES['file']['tmp_name'], $dirFile);
            return new RedirectResponse('home');

}}
    public function listFiles($folder)
    {
        $defaultDir = './files'.$folder.(substr($folder, -1) == '/' ? '' : '/');
        $defaultDir = str_replace('..', '.', $defaultDir);
        if (!is_dir($defaultDir)) {
            return;
        }
        $list = scandir($defaultDir);
        $result = [];
        for ($i = 1; $i < count($list); ++$i) {
            $result[$i]['name'] = $list[$i];
            $result[$i]['url'] = 'explorer'.str_replace('/', ':', $folder.(substr($folder, -1) == '/' ? '' : '/').$list[$i]);
            if (!is_dir($defaultDir.$list[$i])) {
                $result[$i]['size'] = intval(filesize($defaultDir.$list[$i]) / 1024).'ko';
            }
            if ($list[$i] == '..') {
                $aurl = explode(':', $result[$i]['url']);
                array_pop($aurl);
                if (end($aurl) != 'explorer') {
                    array_pop($aurl);
                }
                $result[$i]['url'] = implode(':', $aurl);
            }
        }

        return $result;
    }

    public function rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != '.' && $object != '..') {
                    if (is_dir($dir.'/'.$object)) {
                        self::rrmdir($dir.'/'.$object);
                    } else {
                        unlink($dir.'/'.$object);
                    }
                }
            }
            rmdir($dir);
        }
    }
}

