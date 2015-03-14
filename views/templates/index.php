<?php  //   namespace Views\Templates;

class V_Templates_Main {
	private $sTpl = false;
    private $sErrors = false;
    private $bGetFile = false;
    private $aBlockContent = array();

	/**
	 *
	 */
	public function __construct($sFileName) {
		if (file_exists($sFileName)) {
			$this->sTpl = file_get_contents($sFileName);
		}
	}

    public function getBlock($blockname) {
    	$regex = "#\{\% " . $blockname . "\%\}(.+?)\{\% end" . $blockname . "\%\}#s";
    	preg_match($regex, $this->tpl, $matches);
    	return $matches[1];
	}

    public function newBlock($blockname, $content) {
        $this->aBlockContent[$blockname] .= $this->getBlock($blockname); // Block inhoud ophalen
        
        foreach ($content as $pattern=>$replacement) {                        
			$this->aBlockContent[$blockname] = preg_replace("#\{" . $pattern . "\}#si", $replacement, $this->aBlockContent[$blockname]);
        }
    }

    public function set($pattern, $replacement) {
        $this->tpl = preg_replace("#\{" . $pattern . "\}#si", $replacement, $this->tpl); // {iets} wordt veranderd in iets.
    }

	public function parseTpl() {
      	if (!$this->sErrors) {
           	foreach ($this->aBlockContent as $blockname => $block) {
            	$regex = "#\[start-block " . $blockname . "\](.+?)\[end-block " . $blockname . "\]#s";
            	$this->sTpl = preg_replace($regex, $block, $this->sTpl); //    De inhoud aan de pagina toevoegen.
        	}
           	return $this->sTpl;
        }
        else {
            return $this->errors;
        }
    }
}