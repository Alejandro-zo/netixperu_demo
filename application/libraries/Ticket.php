<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH."/third_party/netix_fpdf/fpdf.php"; 
 
class Ticket extends FPDF { 

    protected $javascript;
    protected $n_js;

    function IncludeJS($script, $isUTF8=false) {
        if(!$isUTF8)
            $script=utf8_encode($script);
        $this->javascript=$script;
    }

    function _putjavascript() {
        $this->_newobj();
        $this->n_js=$this->n;
        $this->_put('<<');
        $this->_put('/Names [(EmbeddedJS) '.($this->n+1).' 0 R]');
        $this->_put('>>');
        $this->_put('endobj');
        $this->_newobj();
        $this->_put('<<');
        $this->_put('/S /JavaScript');
        $this->_put('/JS '.$this->_textstring($this->javascript));
        $this->_put('>>');
        $this->_put('endobj');
    }

    function _putresources() {
        parent::_putresources();
        if (!empty($this->javascript)) {
            $this->_putjavascript();
        }
    }

    function _putcatalog() {
        parent::_putcatalog();
        if (!empty($this->javascript)) {
            $this->_put('/Names <</JavaScript '.($this->n_js).' 0 R>>');
        }
    }

    function AutoPrint($printer=''){
        if($printer){
            $printer = str_replace('\\', '\\\\', $printer);
            $script = "var pp = getPrintParams();";
            $script .= "pp.interactive = pp.constants.interactionLevel.full;";
            //$script .= "pp.interactive = pp.constants.interactionLevel.automatic;";
            $script .= "pp.printerName = '$printer'";
            $script .= "print(pp);";
        }
        else
            $script = 'print(true);';
        $this->IncludeJS($script);
    }



    function pdf_tabla_head($columnas,$medidas,$size){
        $this->SetFont('Arial','B',$size); $this->SetFillColor(20,20,0); $this->SetDrawColor(10,0,0); 
        for($i=0;$i<count($columnas);$i++){
            $this->Cell($medidas[$i],3,utf8_decode($columnas[$i]),0,0,'L');
        }
        $this->Ln();
    }

    var $widths;
    var $aligns;
    var $lineHeight;

    function SetWidths($w){
        $this->widths = $w;
    }
    function SetAligns($a){
        $this->aligns = $a;
    }
    function SetLineHeight($h){
        $this->lineHeight = $h;
    }

    function Row($data){
        $nb=0;

        for($i=0;$i<count($data);$i++){
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        }
        
        $h=$this->lineHeight * $nb;
        $this->CheckPageBreak($h);

        for($i=0;$i<count($data);$i++){
            $this->SetFillColor(255,255,255); $this->SetDrawColor(255,255,255);
            
            $w=$this->widths[$i];
            $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';

            $x=$this->GetX();
            $y=$this->GetY();
            $this->Rect($x,$y,$w,$h);

            $this->MultiCell($w,3,$data[$i],0,$a);
            $this->SetXY($x+$w,$y);
        }
        $this->Ln($h);
    }

    function CheckPageBreak($h){
        if($this->GetY()+$h>$this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w,$txt){
        $cw=&$this->CurrentFont['cw'];
        if($w==0)
            $w=$this->w-$this->rMargin-$this->x;
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace("\r",'',$txt);
        $nb=strlen($s);
        if($nb>0 and $s[$nb-1]=="\n")
            $nb--;
        $sep=-1;
        $i=0;
        $j=0;
        $l=0;
        $nl=1;
        while($i<$nb){
            $c=$s[$i];
            if($c=="\n"){
                $i++;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
                continue;
            }
            if($c==' ')
                $sep=$i;
            $l+=$cw[$c];
            if($l>$wmax){
                if($sep==-1){
                    if($i==$j)
                        $i++;
                }
                else
                    $i=$sep+1;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl;
    }
}