/**
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2016 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/

$(document).ready(function(){
  $('body').on( "click", "#table_generator", function() {
    create_table();
  });
});

    function create_table()  
    {  
     var tbl_display = document.getElementById("tbl_display");
     var tbl_display1 = document.getElementById("tbl_display");  
     var no_rows =  $('.nrows').val(); 
     var no_col =  $('.ncol').val();

     var tbl = document.createElement('table');  
     //checking and adding style  
     //checking which style is selected  

        if ($('.table_bordered').prop('checked')){  
            tbl.setAttribute("class", "table table-bordered table-sizegudie");   
        }  
         if ($('.table_bordered').prop('checked') && $('.table_striped').prop('checked')){  
            tbl.setAttribute("class", "table table-striped table-bordered table-sizegudie");   
        }
        if  ($('.table_striped').prop('checked') && !$('.table_bordered').prop('checked')){  
            tbl.setAttribute("class", "table table-striped table-sizegudie");   
        }
         if (!$('.table_bordered').prop('checked') && !$('.table_striped').prop('checked')){  
            tbl.setAttribute("class", "table table-sizegudie");   
        }   


     var tblHead = document.createElement('thead');  
     tbl.appendChild(tblHead);  
     var tblrow = document.createElement("tr");  
     tblHead.appendChild(tblrow);  
     for(r=0; r< no_col; r++)  
     {  
      var tblHeadCell = document.createElement('th');  
      tblrow.appendChild(tblHeadCell);  
      var thText = document.createTextNode("th"+r);  
      tblHeadCell.appendChild(thText);  
     } 
     if ($('.header_row').prop('checked')){  
      var tblHeadCell = document.createElement('th'); 
      tblHeadCell.className = "nobordered-cell"; 
      tblrow.insertBefore(tblHeadCell,tblrow.firstChild);
      var thText = document.createTextNode("");  
      tblHeadCell.appendChild(thText);   

    }    
     var tblBody = document.createElement("tbody");  
     for (var p = 0; p < no_rows; p++) {  
            // creates a table row  
            var row = document.createElement("tr");  
              
            if ($('.header_row').prop('checked')){  
           var cell = document.createElement("td");  
          cell.className = "bordered-cell";
                var cellText = document.createTextNode("header");  
                cell.appendChild(cellText);  
                row.appendChild(cell);
              }  
            for (var q = 0; q < no_col; q++) {  
                // Create a <td> element and a text node, make the text  
                // node the contents of the <td>, and put the <td> at  
                // the end of the table row  
                var cell = document.createElement("td");  
                var cellText = document.createTextNode("cell"+p+","+q);  
                cell.appendChild(cellText);  
                row.appendChild(cell);  
            } 

            // add the row to the end of the table body  
            tblBody.appendChild(row);  
        }  
        tbl.appendChild(tblBody);  
        //display table 
        AddNotes(tbl);  
      }   

      function AddNotes(content) {
          for (var i = 0, len = tinymce.editors.length; i < len; i++) {
            console.log(tinymce.activeEditor);
           //  tinymce.editors[i].execCommand('mceInsertContent', false , content.outerHTML); 
    }      

    tinymce.activeEditor.execCommand('mceInsertContent', false , content.outerHTML); 
      }
