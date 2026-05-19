<?php
/*******************************************************************************
* FPDF Minimal (Simplified for DSM-WH)                                         *
*******************************************************************************/
class FPDF {
    protected $page;               // current page number
    protected $n;                  // current object number
    protected $offsets;            // array of object offsets
    protected $buffer;             // buffer holding binary data
    protected $pages;              // array containing pages
    protected $state;              // current state
    protected $compress;           // compression flag
    protected $k;                  // scale factor (number of points in user unit)
    protected $DefOrientation;     // default orientation
    protected $CurOrientation;     // current orientation
    protected $PageInfo;           // standard page sizes
    protected $wPt, $hPt;          // dimensions of page in points
    protected $w, $h;              // dimensions of page in user unit
    protected $lMargin;            // left margin
    protected $tMargin;            // top margin
    protected $rMargin;            // right margin
    protected $bMargin;            // page break margin
    protected $cMargin;            // cell margin
    protected $x, $y;              // current position in user unit
    protected $lasth;              // height of last printed cell
    protected $LineWidth;          // line width in user unit
    protected $fontpath;           // path containing fonts
    protected $CoreFonts;          // array of core font names
    protected $fonts;              // array of used fonts
    protected $FontFiles;          // array of font files
    protected $diffs;              // array of encoding differences
    protected $FontFamily;         // current font family
    protected $FontStyle;          // current font style
    protected $underline;          // underlining flag
    protected $CurrentFont;        // current font info
    protected $FontSizePt;         // current font size in points
    protected $FontSize;           // current font size in user unit
    protected $DrawColor;          // commands for drawing color
    protected $FillColor;          // commands for filling color
    protected $TextColor;          // commands for text color
    protected $ColorFlag;          // indicates whether fill and text colors are different
    protected $WithAlpha;          // indicates whether alpha channel is used
    protected $images;             // array of used images
    protected $PageLinks;          // array of links in pages
    protected $links;              // array of internal links
    protected $AutoPageBreak;      // automatic page breaking
    protected $PageBreakTrigger;   // threshold used to trigger page breaks
    protected $InHeader;           // flag set when processing header
    protected $InFooter;           // flag set when processing footer
    protected $AliasNbPages;       // alias for total number of pages
    protected $ZoomMode;           // zoom display mode
    protected $LayoutMode;         // layout display mode
    protected $metadata;           // document properties
    protected $PDFVersion;         // PDF version number

    public function __construct($orientation='P', $unit='mm', $size='A4') {
        $this->state = 0;
        $this->page = 0;
        $this->n = 2;
        $this->buffer = '';
        $this->pages = array();
        $this->PageInfo = array();
        $this->fonts = array();
        $this->FontFiles = array();
        $this->diffs = array();
        $this->images = array();
        $this->links = array();
        $this->InHeader = false;
        $this->InFooter = false;
        $this->lasth = 0;
        $this->fontpath = '';
        if(defined('FPDF_FONTPATH')) $this->fontpath = FPDF_FONTPATH;
        $this->CoreFonts = array('courier', 'helvetica', 'times', 'symbol', 'zapfdingbats');
        $this->k = ($unit=='pt') ? 1 : (($unit=='mm') ? 72/25.4 : (($unit=='cm') ? 72/2.54 : (($unit=='in') ? 72 : 1)));
        $this->SetMargins(10, 10, 10);
        $this->cMargin = $this->lMargin/10;
        $this->LineWidth = 0.567/$this->k;
        $this->SetDisplayMode('fullpage');
        $this->SetAutoPageBreak(true, 20);
        $this->PDFVersion = '1.3';
    }

    public function SetDisplayMode($zoom, $layout='default') {
        $this->ZoomMode = $zoom;
        $this->LayoutMode = $layout;
    }

    public function SetMargins($left, $top, $right=null) {
        $this->lMargin = $left;
        $this->tMargin = $top;
        if($right===null) $right = $left;
        $this->rMargin = $right;
    }

    public function SetAutoPageBreak($auto, $margin=0) {
        $this->AutoPageBreak = $auto;
        $this->bMargin = $margin;
        $this->PageBreakTrigger = $this->h - $margin;
    }

    public function AddPage($orientation='', $size='', $rotation=0) {
        if($this->state==3) die('The document is closed');
        $family = $this->FontFamily;
        $style = $this->FontStyle.($this->underline ? 'U' : '');
        $size = $this->FontSizePt;
        $lw = $this->LineWidth;
        $dc = $this->DrawColor;
        $fc = $this->FillColor;
        $tc = $this->TextColor;
        $cf = $this->ColorFlag;
        if($this->page>0) {
            $this->InFooter = true;
            $this->Footer();
            $this->InFooter = false;
            $this->_endpage();
        }
        $this->_beginpage($orientation, $size, $rotation);
        $this->_out('2 J');
        $this->LineWidth = $lw;
        $this->_out(sprintf('%.2F w', $lw*$this->k));
        if($family) $this->SetFont($family, $style, $size);
        $this->DrawColor = $dc;
        if($dc!='0 G') $this->_out($dc);
        $this->FillColor = $fc;
        if($fc!='0 g') $this->_out($fc);
        $this->TextColor = $tc;
        $this->ColorFlag = $cf;
        $this->InHeader = true;
        $this->Header();
        $this->InHeader = false;
        if($this->LineWidth!=$lw) {
            $this->LineWidth = $lw;
            $this->_out(sprintf('%.2F w', $lw*$this->k));
        }
        if($family) $this->SetFont($family, $style, $size);
        $this->DrawColor = $dc;
        if($dc!='0 G') $this->_out($dc);
        $this->FillColor = $fc;
        if($fc!='0 g') $this->_out($fc);
        $this->TextColor = $tc;
        $this->ColorFlag = $cf;
    }

    public function Header() {}
    public function Footer() {}

    public function SetFont($family, $style='', $size=0) {
        if($family=='') $family = $this->FontFamily;
        else $family = strtolower($family);
        $style = strtoupper($style);
        if(strpos($style, 'U')!==false) {
            $this->underline = true;
            $style = str_replace('U', '', $style);
        } else $this->underline = false;
        if($style=='IB') $style = 'BI';
        if($size==0) $size = $this->FontSizePt;
        if($this->FontFamily==$family && $this->FontStyle==$style && $this->FontSizePt==$size) return;
        $fontkey = $family.$style;
        if(!isset($this->fonts[$fontkey])) {
            if($family=='arial') $family = 'helvetica';
            if(in_array($family, $this->CoreFonts)) {
                if($family=='symbol' || $family=='zapfdingbats') $style = '';
                $fontkey = $family.$style;
                if(!isset($this->fonts[$fontkey])) $this->_loadfont($family, $style);
            } else die('Undefined font: '.$family.' '.$style);
        }
        $this->FontFamily = $family;
        $this->FontStyle = $style;
        $this->FontSizePt = $size;
        $this->FontSize = $size/$this->k;
        $this->CurrentFont = &$this->fonts[$fontkey];
        if($this->page>0) $this->_out(sprintf('BT /F%d %.2F Tf ET', $this->CurrentFont['i'], $this->FontSizePt));
    }

    public function SetXY($x, $y) {
        $this->x = ($x<0) ? $this->w+$x : $x;
        $this->y = ($y<0) ? $this->h+$y : $y;
    }

    public function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='') {
        $k = $this->k;
        if($this->y+$h>$this->PageBreakTrigger && !$this->InHeader && !$this->InFooter && $this->AcceptPageBreak()) {
            $x = $this->x;
            $ws = $this->ws;
            if($ws>0) {
                $this->ws = 0;
                $this->_out('0 Tw');
            }
            $this->AddPage($this->CurOrientation, $this->CurPageSize, $this->CurRotation);
            $this->x = $x;
            if($ws>0) {
                $this->ws = $ws;
                $this->_out(sprintf('%.3F Tw', $ws*$k));
            }
        }
        if($w==0) $w = $this->w-$this->rMargin-$this->x;
        $s = '';
        if($fill || $border==1) {
            if($fill) $op = ($border==1) ? 'B' : 'f';
            else $op = 'S';
            $s = sprintf('%.2F %.2F %.2F %.2F re %s ', $this->x*$k, ($this->h-$this->y)*$k, $w*$k, -$h*$k, $op);
        }
        if(is_string($border)) {
            $x = $this->x;
            $y = $this->y;
            if(strpos($border, 'L')!==false) $s .= sprintf('%.2F %.2F m %.2F %.2F l S ', $x*$k, ($this->h-$y)*$k, $x*$k, ($this->h-($y+$h))*$k);
            if(strpos($border, 'T')!==false) $s .= sprintf('%.2F %.2F m %.2F %.2F l S ', $x*$k, ($this->h-$y)*$k, ($x+$w)*$k, ($this->h-$y)*$k);
            if(strpos($border, 'R')!==false) $s .= sprintf('%.2F %.2F m %.2F %.2F l S ', ($x+$w)*$k, ($this->h-$y)*$k, ($x+$w)*$k, ($this->h-($y+$h))*$k);
            if(strpos($border, 'B')!==false) $s .= sprintf('%.2F %.2F m %.2F %.2F l S ', $x*$k, ($this->h-($y+$h))*$k, ($x+$w)*$k, ($this->h-($y+$h))*$k);
        }
        if($txt!=='') {
            if($align=='R') $dx = $w-$this->cMargin-$this->GetStringWidth($txt);
            elseif($align=='C') $dx = ($w-$this->GetStringWidth($txt))/2;
            else $dx = $this->cMargin;
            if($this->ColorFlag) $s .= 'q '.$this->TextColor.' ';
            $txt2 = str_replace(array(')', '(', '\\'), array('\\)', '\\(', '\\\\'), $txt);
            $s .= sprintf('BT %.2F %.2F Td (%s) Tj ET', ($this->x+$dx)*$k, ($this->h-($this->y+0.5*$h+0.3*$this->FontSize))*$k, $txt2);
            if($this->underline) $s .= ' '.$this->_dounderline($this->x+$dx, $this->y+0.5*$h+0.3*$this->FontSize, $txt);
            if($this->ColorFlag) $s .= ' Q';
            if($link) $this->Link($this->x+$dx, $this->y+0.5*$h-0.5*$this->FontSize, $this->GetStringWidth($txt), $this->FontSize, $link);
        }
        if($s) $this->_out($s);
        $this->lasth = $h;
        if($ln>0) {
            $this->y += $h;
            if($ln==1) $this->x = $this->lMargin;
        } else $this->x += $w;
    }

    public function Output($name='', $dest='') {
        if($this->state<3) $this->Close();
        $dest = strtoupper($dest);
        if($dest=='') {
            if($name=='') {
                $name = 'doc.pdf';
                $dest = 'I';
            } else $dest = 'F';
        }
        switch($dest) {
            case 'I':
                header('Content-Type: application/pdf');
                header('Content-Disposition: inline; filename="'.$name.'"');
                echo $this->buffer;
                break;
            case 'D':
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="'.$name.'"');
                echo $this->buffer;
                break;
        }
    }

    // --- Private Methods (Minimal implementation) ---
    public function Ln($h=null) {
        $this->x = $this->lMargin;
        if($h===null) $this->y += $this->lasth;
        else $this->y += $h;
    }

    public function AcceptPageBreak() {
        return $this->AutoPageBreak;
    }

    protected function _beginpage($orientation, $size, $rotation) {
        $this->page++;
        $this->pages[$this->page] = '';
        $this->state = 2;
        $this->x = $this->lMargin;
        $this->y = $this->tMargin;
        $this->FontFamily = '';
        if($orientation=='') $orientation = $this->DefOrientation;
        $this->CurOrientation = $orientation;
        $this->w = 210; $this->h = 297; // A4
        $this->wPt = $this->w*$this->k;
        $this->hPt = $this->h*$this->k;
        $this->PageBreakTrigger = $this->h-$this->bMargin;
    }

    protected function _out($s) {
        if($this->state==2) $this->pages[$this->page] .= $s."\n";
        else $this->buffer .= $s."\n";
    }

    protected function _loadfont($family, $style) {
        $i = count($this->fonts)+1;
        $this->fonts[$family.$style] = array('i'=>$i, 'type'=>'core', 'name'=>$this->_getfontname($family, $style), 'up'=>-100, 'ut'=>50);
    }

    protected function _getfontname($family, $style) {
        $name = ucfirst($family);
        if($style=='BI') $name .= '-BoldItalic';
        elseif($style=='B') $name .= '-Bold';
        elseif($style=='I') $name .= '-Italic';
        return $name;
    }

    public function GetStringWidth($s) {
        $cw = &$this->CurrentFont['cw'];
        $w = 0; $l = strlen($s);
        for($i=0;$i<$l;$i++) $w += 100; // Minimal estimate
        return $w*$this->FontSize/1000;
    }

    public function Close() {
        if($this->state==3) return;
        if($this->page==0) $this->AddPage();
        $this->_endpage();
        $this->_enddoc();
    }

    protected function _endpage() { $this->state = 1; }
    protected function _enddoc() {
        $this->_putheader();
        $this->_putpages();
        $this->_putresources();
        $this->_putinfo();
        $this->_putcatalog();
        $this->_puttrailer();
        $this->state = 3;
    }

    protected function _putheader() { $this->_out('%PDF-'.$this->PDFVersion); }
    protected function _puttrailer() {
        $this->_out('trailer');
        $this->_out('<<');
        $this->_out('/Size '.($this->n+1));
        $this->_out('/Root '.$this->n.' 0 R');
        $this->_out('/Info '.($this->n-1).' 0 R');
        $this->_out('>>');
        $this->_out('startxref');
        $this->_out($this->offsets[$this->n+1]);
        $this->_out('%%EOF');
    }
    protected function _putpages() {
        $nb = $this->page;
        for($n=1;$n<=$nb;$n++) $this->PageInfo[$n]['n'] = $this->n + 1 + 2*($n-1);
        for($n=1;$n<=$nb;$n++) {
            $this->_newobj();
            $this->_out('<< /Type /Page /Parent 1 0 R /Resources 2 0 R /Contents '.($this->n+1).' 0 R >>');
            $this->_out('endobj');
            $p = $this->pages[$n];
            $this->_newobj();
            $this->_out('<< /Length '.strlen($p).' >>');
            $this->_out('stream');
            $this->_out($p);
            $this->_out('endstream');
            $this->_out('endobj');
        }
        $this->offsets[1] = strlen($this->buffer);
        $this->_out('1 0 obj');
        $this->_out('<< /Type /Pages /Kids [');
        for($n=1;$n<=$nb;$n++) $this->_out($this->PageInfo[$n]['n'].' 0 R');
        $this->_out('] /Count '.$nb.' >>');
        $this->_out('endobj');
    }
    protected function _putresources() {
        $this->offsets[2] = strlen($this->buffer);
        $this->_out('2 0 obj');
        $this->_out('<< /ProcSet [/PDF /Text /ImageB /ImageC /ImageI]');
        $this->_out('/Font <<');
        foreach($this->fonts as $font) $this->_out('/F'.$font['i'].' '.$font['n'].' 0 R');
        $this->_out('>>');
        $this->_out('>>');
        $this->_out('endobj');
        foreach($this->fonts as $font) {
            $this->_newobj($font['n']);
            $this->_out('<< /Type /Font /Subtype /Type1 /BaseFont /'.$font['name'].' /Encoding /WinAnsiEncoding >>');
            $this->_out('endobj');
        }
    }
    protected function _putinfo() {
        $this->_newobj();
        $this->_out('<< /Producer (DSM-WH Native PHP) /CreationDate (D:'.date('YmdHis').') >>');
        $this->_out('endobj');
    }
    protected function _putcatalog() {
        $this->_newobj();
        $this->_out('<< /Type /Catalog /Pages 1 0 R >>');
        $this->_out('endobj');
    }
    protected function _newobj($n=null) {
        if(!$n) $this->n++;
        $this->offsets[$this->n] = strlen($this->buffer);
        $this->_out($this->n.' 0 obj');
    }
}
