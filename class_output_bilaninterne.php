<?php
/*
 *   This file is part of NOALYSS.
 *
 *   NOALYSS is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   NOALYSS is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with NOALYSS; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/*!
 * \file class_output_bilaninterne.php
 * \brief Result display and export methods
 * \author T. Nancy
 * \version 0.1
 * \date 10 janvier 201
*/

require_once NOALYSS_INCLUDE. '/lib/class_database.php';
require_once NOALYSS_INCLUDE. '/lib/class_noalyss_csv.php'; 
require_once NOALYSS_INCLUDE. '/lib/class_pdf.php'; 
require_once NOALYSS_INCLUDE. '/ext/bilan_interne/class_acc_bilaninterne.php';
require_once NOALYSS_INCLUDE. '/ext/bilan_interne/class_output_bilaninterne.php';
require_once NOALYSS_INCLUDE. '/class/class_periode.php';

/*! 
 * \class output_bilaninterne
 * \brief Generates html output and exports in PDF and CSV format 
 */
class output_bilaninterne
{
    public $from;
    public $to;

    function output_html($result,$cn){
    /*!Creates HTML presentation of the result table
     *\param $result, the current csv line
     *\param $cn the current connection to the database
    */
        $periode   = new Periode($cn);
        $date_from = $periode->first_day($this->from);
        $date_to   = $periode->last_day($this->to);
        echo '<div class="content">';
            echo '<hr>';
            // Display results

            echo "<h2 class=\"info\"> période du ".$date_from." au ".$date_to."</h2>";
            echo '<table id="t_balance" width="90%">';
                echo '<th>Libell&eacute;</th>';
                echo '<th>Poste Comptable</th>';
                echo '<th>Solde</th>';
            $i=0;
            bcscale(2);
            foreach ($result as $r){
                //print_r($r);echo '<br>';
                $i++;
                //Line background
                if ( $i%2 == 0 )
                    $tr="even";
                else
                    $tr="odd";
                //Javascript "view_history" set
                $view_history= sprintf('<A class="detail" style="text-decoration:underline" HREF="javascript:view_history_account(\'%s\',\'%s\')" >%s</A>',
                                       $r['poste'], $gDossier, $r['poste']);

                if ($r['linetype'] != 'leaf'){
                    if ($r['linetype'] == 'tittle'){
                        $tittle_style = 'style="font-weight:bold;font-size:150%;text-align:center;"';
                        //echo $style.'<br>';
                        echo '<TR >';
                            echo td($r['label'], $tittle_style);
                            echo td('','style="font-weight:bold;"');
                            echo td('','style="font-weight:bold;text-align:right;"');
                        echo '</TR>';
                    }
                    else {
                        $indent_step= 20;
                        $indent_length = $indent_step * ($r['linestyle'] - 1);
                        $indent_style = 'padding-left:'.strval($indent_length) . 'px;';
                        $indent_leaf_style = 'padding-left:' . strval($indent_length+$indent_step) . 'px;';
                        echo '<TR >';
                            echo td($r['label'],'style="font-weight:bold;'.$indent_style.'"');
                            echo td($r['poste'],'style="font-weight:bold;"');
                            echo td(nbm($r['solde']),'style="font-weight:bold;text-align:right;"');
                        echo '</TR>';
                    }
                }
                else {
                    echo '<TR class="'.$tr.'">';
                        //echo td(h($justification.$decalage.$r['label']));
                        echo td(h($r['label']),'style="' . $indent_leaf_style . '"');
                        echo td($view_history);
                        echo td(nbm($r['solde']),'style="text-align:right;"');
                    echo '</TR>';
                }
            }
            echo '</table>';
        echo '</div>';
    }    
    
    function output_pdf($result,$cn){
    /*!Creates PDF output of the result table
     *\param $result, the current csv line
     *\param $cn the current connection to the database
    */
        $periode=new Periode($cn);
        $date_limit_start=$periode->first_day($this->from);
        $date_limit_end=$periode->last_day($this->to);

        $per_text="  du ".$date_limit_start." au ".$date_limit_end;
        $pdf= new PDF($cn);
        $pdf->setDossierInfo(" Bilan interne  ".$per_text);
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetAuthor('NOALYSS');
        $pdf->SetFont('DejaVuCond','',7);
        $pdf->setTitle("Balance interne",true);

        $pdf->LongLine(140,6,'Libellé');
        $pdf->Cell(25,6,'poste');
        $pdf->Cell(25,6,'Montant',0,0,'R');
        $pdf->Ln();

        bcscale(2);
        $i=0;
        foreach ($result as $r){
            $pdf->SetFont('DejaVuCond','',8);
            //Line background
            if ( $i%2 == 0 ){
                $pdf->SetFillColor(220,221,255);
                $fill="even";
            }
            else {
                $fill="odd";
                $pdf->SetFillColor(255,255,255);
            }
            $i++;
            if ($r['linetype'] != 'leaf'){
                $i=0;
                if ($r['linetype'] == 'tittle'){
                    //Tittle line
                    $pdf->SetFont('DejaVu','B',8);
                    $pdf->LongLine(190,6,$r['label']);
                }
                else {
                    //BNB synthetic line
                    $pdf->SetFont('DejaVu','BI',7);                
                    $pdf->Cell(140,6,$r['label'],0,0,'L',0,0);
                    $pdf->Cell(25,6,$r['poste'],0,0,'L',0);
                    $pdf->Cell(25,6,nbm($r['solde']),0,0,'R',0);
                }
            }
            else {
                //Leaf rows building
                $pdf->Cell(140,6,$r['label'],0,0,'L',$fill);
                $pdf->Cell(25,6,$r['poste'],0,0,'L',$fill);
                $pdf->Cell(25,6,nbm($r['solde']),0,0,'R',$fill);
            }
            $pdf->Ln();
        }
        $fDate=date('dmy-Hi');
        $pdf->Output('bilaninterne-'.$fDate.'.pdf','D');
    }
    
    function output_csv($result,$cn){
    /*!Creates CSV output of the result table
     *\param $result, the current csv line
     *\param $cn the current connection to the database
    */
        $periode=new Periode($cn);
        $date_limit_start = $periode->first_day($this->from);
        $date_limit_end   = $periode->last_day($this->to);
        $per_text="Bilan interne du ".$date_limit_start." au ".$date_limit_end;
        
        $csv = new Noalyss_Csv("bilaninterne");
        $csv->send_header();
        $line_header =  array('','',$per_text,'','');
        $csv->write_header($line_header);
        $line_header =  array('linetype','linestyle','label','poste','solde');
        $csv->write_header($line_header);
        $r = "";
        foreach ($result as $row) {
            foreach ($line_header as $key) {
                $csv->add($row[$key],"text");
            }
            //echo "\n";
            $csv->write();
        }
        return($r);
    }
}