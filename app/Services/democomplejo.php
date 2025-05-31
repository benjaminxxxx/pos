<?php
object(Greenter\Model\Sale\Invoice)#5 (51) {
["ublVersion":protected]=> string(3) "2.1" 
["tipoDoc":protected]=> string(2) "01" 
["serie":protected]=> string(4) "F001" 
["correlativo":protected]=> string(3) "126" 
["fechaEmision":protected]=> object(DateTime)#7 (3) 
 { 
  ["date"]=> string(26) "2025-05-31 02:22:38.010144" 
  ["timezone_type"]=> int(3) 
  ["timezone"]=> string(3) "UTC" 
} 
["company":protected]=> object(Greenter\Model\Company\Company)#9 (6) 
{ 
  ["ruc":"Greenter\Model\Company\Company":private]=> string(11) "20123456789" ["razonSocial":"Greenter\Model\Company\Company":private]=> string(15) "GREENTER S.A.C." 
  ["nombreComercial":"Greenter\Model\Company\Company":private]=> string(8) "GREENTER" ["address":"Greenter\Model\Company\Company":private]=> object(Greenter\Model\Company\Address)#10 (8) 
  { 
    ["ubigueo":"Greenter\Model\Company\Address":private]=> string(6) "150101" ["codigoPais":"Greenter\Model\Company\Address":private]=> string(2) "PE" ["departamento":"Greenter\Model\Company\Address":private]=> string(4) "LIMA" ["provincia":"Greenter\Model\Company\Address":private]=> string(4) "LIMA" ["distrito":"Greenter\Model\Company\Address":private]=> string(4) "LIMA" ["urbanizacion":"Greenter\Model\Company\Address":private]=> string(10) "CASUARINAS" 
    ["direccion":"Greenter\Model\Company\Address":private]=> string(16) "AV NEW DEÁL 123" 
    ["codLocal":"Greenter\Model\Company\Address":private]=> string(4) "0000" } ["email":"Greenter\Model\Company\Company":private]=> string(18) "admin@greenter.com" 
    ["telephone":"Greenter\Model\Company\Company":private]=> string(9) "01-234455" 
  } 
  ["client":protected]=> object(Greenter\Model\Client\Client)#11 (6) 
  { 
    ["tipoDoc":"Greenter\Model\Client\Client":private]=> string(1) "6" ["numDoc":"Greenter\Model\Client\Client":private]=> string(11) "20000000001" ["rznSocial":"Greenter\Model\Client\Client":private]=> string(16) "EMPRESA 1 S.A.C." 
    ["address":"Greenter\Model\Client\Client":private]=> object(Greenter\Model\Company\Address)#12 (8) 
    { 
      ["ubigueo":"Greenter\Model\Company\Address":private]=> NULL ["codigoPais":"Greenter\Model\Company\Address":private]=> string(2) "PE" ["departamento":"Greenter\Model\Company\Address":private]=> NULL ["provincia":"Greenter\Model\Company\Address":private]=> NULL ["distrito":"Greenter\Model\Company\Address":private]=> NULL ["urbanizacion":"Greenter\Model\Company\Address":private]=> NULL ["direccion":"Greenter\Model\Company\Address":private]=> string(72) "JR. NIQUEL MZA. F LOTE. 3 URB. INDUSTRIAL INFAÑTAS - LIMA - LIMA -PERU" ["codLocal":"Greenter\Model\Company\Address":private]=> string(4) "0000" 
    } 
    ["email":"Greenter\Model\Client\Client":private]=> string(15) "client@corp.com" ["telephone":"Greenter\Model\Client\Client":private]=> string(9) "01-445566" 
  } 
  ["tipoMoneda":protected]=> string(3) "PEN" 
  ["sumOtrosCargos":protected]=> NULL 
  ["mtoOperGravadas":protected]=> float(200) 
  ["mtoOperInafectas":protected]=> float(200) 
  ["mtoOperExoneradas":protected]=> float(100) 
  ["mtoOperExportacion":protected]=> NULL 
  ["mtoOperGratuitas":protected]=> float(300) 
  ["mtoIGVGratuitas":protected]=> float(18) 
  ["mtoIGV":protected]=> float(36) 
  ["mtoBaseIvap":protected]=> NULL 
  ["mtoIvap":protected]=> NULL 
  ["mtoBaseIsc":protected]=> NULL 
  ["mtoISC":protected]=> NULL 
  ["mtoBaseOth":protected]=> NULL 
  ["mtoOtrosTributos":protected]=> NULL 
  ["icbper":protected]=> NULL 
  ["totalImpuestos":protected]=> float(36) 
  ["redondeo":protected]=> NULL 
  ["mtoImpVenta":protected]=> float(536) 
  ["details":protected]=> array(5) 
  { 
    [0]=> object(Greenter\Model\Sale\SaleDetail)#13 (28) 
    { 
      ["unidad":"Greenter\Model\Sale\SaleDetail":private]=> string(3) "NIU" 
      ["cantidad":"Greenter\Model\Sale\SaleDetail":private]=> float(2) 
      ["codProducto":"Greenter\Model\Sale\SaleDetail":private]=> string(4) "P001" 
      ["codProdSunat":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["codProdGS1":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["descripcion":"Greenter\Model\Sale\SaleDetail":private]=> string(6) "PROD 1" 
      ["mtoValorUnitario":"Greenter\Model\Sale\SaleDetail":private]=> float(100) 
      ["cargos":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["descuentos":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["descuento":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["mtoBaseIgv":"Greenter\Model\Sale\SaleDetail":private]=> float(200) 
      ["porcentajeIgv":"Greenter\Model\Sale\SaleDetail":private]=> float(18) 
      ["igv":"Greenter\Model\Sale\SaleDetail":private]=> float(36) 
      ["tipAfeIgv":"Greenter\Model\Sale\SaleDetail":private]=> string(2) "10" 
      ["mtoBaseIsc":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["porcentajeIsc":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["isc":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["tipSisIsc":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["mtoBaseOth":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["porcentajeOth":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["otroTributo":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["icbper":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["factorIcbper":"Greenter\Model\Sale\SaleDetail":private]=> float(0.3) 
      ["totalImpuestos":"Greenter\Model\Sale\SaleDetail":private]=> float(36) 
      ["mtoPrecioUnitario":"Greenter\Model\Sale\SaleDetail":private]=> float(118) 
      ["mtoValorVenta":"Greenter\Model\Sale\SaleDetail":private]=> float(200)
      ["mtoValorGratuito":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["atributos":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
    } 
    [1]=> object(Greenter\Model\Sale\SaleDetail)#14 (28) 
    { 
      ["unidad":"Greenter\Model\Sale\SaleDetail":private]=> string(2) "KG" 
      ["cantidad":"Greenter\Model\Sale\SaleDetail":private]=> float(2) 
      ["codProducto":"Greenter\Model\Sale\SaleDetail":private]=> string(4) "P002" 
      ["codProdSunat":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["codProdGS1":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["descripcion":"Greenter\Model\Sale\SaleDetail":private]=> string(6) "PROD 2" 
      ["mtoValorUnitario":"Greenter\Model\Sale\SaleDetail":private]=> float(50) 
      ["cargos":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["descuentos":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["descuento":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["mtoBaseIgv":"Greenter\Model\Sale\SaleDetail":private]=> float(100) 
      ["porcentajeIgv":"Greenter\Model\Sale\SaleDetail":private]=> float(0) 
      ["igv":"Greenter\Model\Sale\SaleDetail":private]=> float(0) 
      ["tipAfeIgv":"Greenter\Model\Sale\SaleDetail":private]=> string(2) "20" 
      ["mtoBaseIsc":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["porcentajeIsc":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["isc":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["tipSisIsc":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["mtoBaseOth":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["porcentajeOth":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["otroTributo":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["icbper":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["factorIcbper":"Greenter\Model\Sale\SaleDetail":private]=> float(0.3) 
      ["totalImpuestos":"Greenter\Model\Sale\SaleDetail":private]=> float(0) 
      ["mtoPrecioUnitario":"Greenter\Model\Sale\SaleDetail":private]=> float(50) 
      ["mtoValorVenta":"Greenter\Model\Sale\SaleDetail":private]=> float(100) 
      ["mtoValorGratuito":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["atributos":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
    } 
    [2]=> object(Greenter\Model\Sale\SaleDetail)#15 (28) 
    { 
      ["unidad":"Greenter\Model\Sale\SaleDetail":private]=> string(3) "NIU" 
      ["cantidad":"Greenter\Model\Sale\SaleDetail":private]=> float(2) 
      ["codProducto":"Greenter\Model\Sale\SaleDetail":private]=> string(4) "P003" 
      ["codProdSunat":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["codProdGS1":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["descripcion":"Greenter\Model\Sale\SaleDetail":private]=> string(6) "PROD 3" 
      ["mtoValorUnitario":"Greenter\Model\Sale\SaleDetail":private]=> float(100) 
      ["cargos":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["descuentos":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["descuento":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["mtoBaseIgv":"Greenter\Model\Sale\SaleDetail":private]=> float(200) 
      ["porcentajeIgv":"Greenter\Model\Sale\SaleDetail":private]=> float(0) 
      ["igv":"Greenter\Model\Sale\SaleDetail":private]=> float(0) 
      ["tipAfeIgv":"Greenter\Model\Sale\SaleDetail":private]=> string(2) "30" 
      ["mtoBaseIsc":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["porcentajeIsc":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["isc":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["tipSisIsc":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["mtoBaseOth":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["porcentajeOth":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["otroTributo":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["icbper":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["factorIcbper":"Greenter\Model\Sale\SaleDetail":private]=> float(0.3) 
      ["totalImpuestos":"Greenter\Model\Sale\SaleDetail":private]=> float(0) 
      ["mtoPrecioUnitario":"Greenter\Model\Sale\SaleDetail":private]=> float(100) 
      ["mtoValorVenta":"Greenter\Model\Sale\SaleDetail":private]=> float(200) 
      ["mtoValorGratuito":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["atributos":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
    } 
    [3]=> object(Greenter\Model\Sale\SaleDetail)#16 (28) 
    { 
      ["unidad":"Greenter\Model\Sale\SaleDetail":private]=> string(3) "NIU" 
      ["cantidad":"Greenter\Model\Sale\SaleDetail":private]=> float(1) 
      ["codProducto":"Greenter\Model\Sale\SaleDetail":private]=> string(4) "P004" 
      ["codProdSunat":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["codProdGS1":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["descripcion":"Greenter\Model\Sale\SaleDetail":private]=> string(6) "PROD 4" 
      ["mtoValorUnitario":"Greenter\Model\Sale\SaleDetail":private]=> float(0) 
      ["cargos":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["descuentos":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["descuento":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["mtoBaseIgv":"Greenter\Model\Sale\SaleDetail":private]=> float(100) 
      ["porcentajeIgv":"Greenter\Model\Sale\SaleDetail":private]=> float(18) 
      ["igv":"Greenter\Model\Sale\SaleDetail":private]=> float(18) 
      ["tipAfeIgv":"Greenter\Model\Sale\SaleDetail":private]=> string(2) "13" 
      ["mtoBaseIsc":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["porcentajeIsc":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["isc":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["tipSisIsc":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["mtoBaseOth":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["porcentajeOth":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["otroTributo":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["icbper":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
      ["factorIcbper":"Greenter\Model\Sale\SaleDetail":private]=> float(0.3) 
      ["totalImpuestos":"Greenter\Model\Sale\SaleDetail":private]=> float(18) 
      ["mtoPrecioUnitario":"Greenter\Model\Sale\SaleDetail":private]=> float(0) 
      ["mtoValorVenta":"Greenter\Model\Sale\SaleDetail":private]=> float(100) 
      ["mtoValorGratuito":"Greenter\Model\Sale\SaleDetail":private]=> float(100) 
      ["atributos":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
    } 
    [4]=> object(Greenter\Model\Sale\SaleDetail)#17 (28) 
    { 
      ["unidad":"Greenter\Model\Sale\SaleDetail":private]=> string(3) "NIU" 
      ["cantidad":"Greenter\Model\Sale\SaleDetail":private]=> float(2) ["codProducto":"Greenter\Model\Sale\SaleDetail":private]=> string(4) "P005" ["codProdSunat":"Greenter\Model\Sale\SaleDetail":private]=> NULL ["codProdGS1":"Greenter\Model\Sale\SaleDetail":private]=> NULL ["descripcion":"Greenter\Model\Sale\SaleDetail":private]=> string(6) "PROD 5" ["mtoValorUnitario":"Greenter\Model\Sale\SaleDetail":private]=> float(0) ["cargos":"Greenter\Model\Sale\SaleDetail":private]=> NULL ["descuentos":"Greenter\Model\Sale\SaleDetail":private]=> NULL ["descuento":"Greenter\Model\Sale\SaleDetail":private]=> NULL ["mtoBaseIgv":"Greenter\Model\Sale\SaleDetail":private]=> float(200) ["porcentajeIgv":"Greenter\Model\Sale\SaleDetail":private]=> float(0) ["igv":"Greenter\Model\Sale\SaleDetail":private]=> float(0) ["tipAfeIgv":"Greenter\Model\Sale\SaleDetail":private]=> string(2) "32" ["mtoBaseIsc":"Greenter\Model\Sale\SaleDetail":private]=> NULL ["porcentajeIsc":"Greenter\Model\Sale\SaleDetail":private]=> NULL ["isc":"Greenter\Model\Sale\SaleDetail":private]=> NULL ["tipSisIsc":"Greenter\Model\Sale\SaleDetail":private]=> NULL ["mtoBaseOth":"Greenter\Model\Sale\SaleDetail":private]=> NULL ["porcentajeOth":"Greenter\Model\Sale\SaleDetail":private]=> NULL ["otroTributo":"Greenter\Model\Sale\SaleDetail":private]=> NULL ["icbper":"Greenter\Model\Sale\SaleDetail":private]=> NULL ["factorIcbper":"Greenter\Model\Sale\SaleDetail":private]=> float(0.3) ["totalImpuestos":"Greenter\Model\Sale\SaleDetail":private]=> float(0) ["mtoPrecioUnitario":"Greenter\Model\Sale\SaleDetail":private]=> float(0) ["mtoValorVenta":"Greenter\Model\Sale\SaleDetail":private]=> float(200) ["mtoValorGratuito":"Greenter\Model\Sale\SaleDetail":private]=> float(100) ["atributos":"Greenter\Model\Sale\SaleDetail":private]=> NULL 
    } 
  } 
  ["legends":protected]=> array(2) 
  { 
    [0]=> object(Greenter\Model\Sale\Legend)#18 (2) { ["code":"Greenter\Model\Sale\Legend":private]=> string(4) "1000" ["value":"Greenter\Model\Sale\Legend":private]=> string(46) "SON QUINIENTOS TREINTA Y SEIS CON OO/100 SOLES" } [1]=> object(Greenter\Model\Sale\Legend)#19 (2) { ["code":"Greenter\Model\Sale\Legend":private]=> string(4) "1002" ["value":"Greenter\Model\Sale\Legend":private]=> string(69) "TRANSFERENCIA GRATUITA DE UN BIEN Y/O SERVICIO PRESTADO GRATUITAMENTE" } } ["guias":protected]=> NULL ["relDocs":protected]=> NULL ["compra":protected]=> NULL ["formaPago":protected]=> object(Greenter\Model\Sale\FormaPagos\FormaPagoContado)#8 (3) { ["moneda":protected]=> NULL ["tipo":protected]=> string(7) "Contado" ["monto":protected]=> NULL } ["cuotas":protected]=> NULL ["tipoOperacion":"Greenter\Model\Sale\Invoice":private]=> string(4) "0101" ["fecVencimiento":"Greenter\Model\Sale\Invoice":private]=> object(DateTime)#6 (3) { ["date"]=> string(26) "2025-05-31 02:22:38.010140" ["timezone_type"]=> int(3) ["timezone"]=> string(3) "UTC" } ["sumDsctoGlobal":"Greenter\Model\Sale\Invoice":private]=> NULL ["mtoDescuentos":"Greenter\Model\Sale\Invoice":private]=> NULL ["sumOtrosDescuentos":"Greenter\Model\Sale\Invoice":private]=> NULL ["descuentos":"Greenter\Model\Sale\Invoice":private]=> NULL ["cargos":"Greenter\Model\Sale\Invoice":private]=> NULL ["mtoCargos":"Greenter\Model\Sale\Invoice":private]=> NULL ["totalAnticipos":"Greenter\Model\Sale\Invoice":private]=> NULL ["perception":"Greenter\Model\Sale\Invoice":private]=> NULL ["guiaEmbebida":"Greenter\Model\Sale\Invoice":private]=> NULL ["anticipos":"Greenter\Model\Sale\Invoice":private]=> NULL ["detraccion":"Greenter\Model\Sale\Invoice":private]=> NULL ["seller":"Greenter\Model\Sale\Invoice":private]=> NULL ["valorVenta":"Greenter\Model\Sale\Invoice":private]=> float(500) ["subTotal":"Greenter\Model\Sale\Invoice":private]=> float(536) ["observacion":"Greenter\Model\Sale\Invoice":private]=> NULL ["direccionEntrega":"Greenter\Model\Sale\Invoice":private]=> NULL }
Time: 00:00:00:017