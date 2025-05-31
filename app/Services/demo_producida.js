const invoiceData = {
  ublVersion: "2.1",
  tipoDoc: "01",
  serie: "F001",
  correlativo: "126",
  fechaEmision: "2025-05-31T02:39:02.683353Z",
   company: {
    ruc: "20123456789",
    razonSocial: "GREENTER S.A.C.",
    nombreComercial: "GREENTER",
    address: {
      ubigueo: "150101",
      codigoPais: "PE",
      departamento: "LIMA",
      provincia: "LIMA",
      distrito: "LIMA",
      urbanizacion: "CASUARINAS",
      direccion: "AV NEW DEÁL 123",
      codLocal: "0000",
    },
    //email: "admin@greenter.com",
    //telephone: "01-234455",
  },
  client: {
    tipoDoc: "6",
    numDoc: "65465465454",
    rznSocial: "juan",
    address: null,
    email: null,
    telephone: null,
  },
  tipoMoneda: "PEN",
  sumOtrosCargos: null, 
  mtoOperGravadas: 200.0,
  mtoOperInafectas: 200.0,
  mtoOperExoneradas: 100.0,
  mtoOperExportacion: null,
  mtoOperGratuitas: 300.0,
  mtoIGVGratuitas: 18.0,
  mtoIGV: 36.0,
  mtoBaseIvap: null,
  mtoIvap: null,
  mtoBaseIsc: null,
  mtoISC: null,
  mtoBaseOth: null,
  mtoOtrosTributos: null,
  icbper: 0.0,
  totalImpuestos: 36.0,
  redondeo: 0.0,
  mtoImpVenta: 536.0,
    details: [
    {
      unidad: "NIU",
      cantidad: 2,
      codProducto: "P001",
      codProdSunat: null,
      codProdGS1: null,
      descripcion: "PROD 1",
      mtoValorUnitario: 100,
      cargos: null,
      descuentos: null,
      descuento: null,
      mtoBaseIgv: 200,
      porcentajeIgv: 18,
      igv: 36,
      tipAfeIgv: "10",
      mtoBaseIsc: null,
      porcentajeIsc: null,
      isc: null,
      tipSisIsc: null,
      mtoBaseOth: null,
      porcentajeOth: null,
      otroTributo: null,
      icbper: null,
      factorIcbper: 0.3,
      totalImpuestos: 36,
      mtoPrecioUnitario: 118,
      mtoValorVenta: 200,
      mtoValorGratuito: null,
      atributos: null,
    },
  
  #details: array:5 [▼
    0 => 
Greenter\Model\Sale
\
SaleDetail
 {#1576 ▼
      -unidad: "NIU"
      -cantidad: 2.0
      -codProducto: "P000001"
      -codProdSunat: null
      -codProdGS1: null
      -descripcion: "PROD 1"
      -mtoValorUnitario: 100.0
      -cargos: null
      -descuentos: null
      -descuento: null
      -mtoBaseIgv: 200.0
      -porcentajeIgv: 18.0
      -igv: 36.0
      -tipAfeIgv: "10"
      -mtoBaseIsc: null
      -porcentajeIsc: null
      -isc: null
      -tipSisIsc: null
      -mtoBaseOth: null
      -porcentajeOth: null
      -otroTributo: null
      -icbper: null
      -factorIcbper: null
      -totalImpuestos: 36.0
      -mtoPrecioUnitario: 118.0
      -mtoValorVenta: 200.0
      -mtoValorGratuito: null
      -atributos: null
    }
    1 => 
Greenter\Model\Sale
\
SaleDetail
 {#1568 ▼
      -unidad: "KG"
      -cantidad: 2.0
      -codProducto: "P000001"
      -codProdSunat: null
      -codProdGS1: null
      -descripcion: "PROD 2"
      -mtoValorUnitario: 50.0
      -cargos: null
      -descuentos: null
      -descuento: null
      -mtoBaseIgv: 100.0
      -porcentajeIgv: 0.0
      -igv: 0.0
      -tipAfeIgv: "20"
      -mtoBaseIsc: null
      -porcentajeIsc: null
      -isc: null
      -tipSisIsc: null
      -mtoBaseOth: null
      -porcentajeOth: null
      -otroTributo: null
      -icbper: null
      -factorIcbper: null
      -totalImpuestos: 0.0
      -mtoPrecioUnitario: 50.0
      -mtoValorVenta: 100.0
      -mtoValorGratuito: null
      -atributos: null
    }
    2 => 
Greenter\Model\Sale
\
SaleDetail
 {#1578 ▼
      -unidad: "NIU"
      -cantidad: 2.0
      -codProducto: "P000001"
      -codProdSunat: null
      -codProdGS1: null
      -descripcion: "PROD 3"
      -mtoValorUnitario: 100.0
      -cargos: null
      -descuentos: null
      -descuento: null
      -mtoBaseIgv: 200.0
      -porcentajeIgv: 0.0
      -igv: 0.0
      -tipAfeIgv: "30"
      -mtoBaseIsc: null
      -porcentajeIsc: null
      -isc: null
      -tipSisIsc: null
      -mtoBaseOth: null
      -porcentajeOth: null
      -otroTributo: null
      -icbper: null
      -factorIcbper: null
      -totalImpuestos: 0.0
      -mtoPrecioUnitario: 100.0
      -mtoValorVenta: 200.0
      -mtoValorGratuito: null
      -atributos: null
    }
    3 => 
Greenter\Model\Sale
\
SaleDetail
 {#1571 ▼
      -unidad: "NIU"
      -cantidad: 1.0
      -codProducto: "N272383"
      -codProdSunat: null
      -codProdGS1: null
      -descripcion: "PROD 4"
      -mtoValorUnitario: 0.0
      -cargos: null
      -descuentos: null
      -descuento: null
      -mtoBaseIgv: 100.0
      -porcentajeIgv: 18.0
      -igv: 18.0
      -tipAfeIgv: "13"
      -mtoBaseIsc: null
      -porcentajeIsc: null
      -isc: null
      -tipSisIsc: null
      -mtoBaseOth: null
      -porcentajeOth: null
      -otroTributo: null
      -icbper: null
      -factorIcbper: null
      -totalImpuestos: 18.0
      -mtoPrecioUnitario: 0.0
      -mtoValorVenta: 100.0
      -mtoValorGratuito: null
      -atributos: null
    }
    4 => 
Greenter\Model\Sale
\
SaleDetail
 {#1572 ▼
      -unidad: "NIU"
      -cantidad: 2.0
      -codProducto: "N455529"
      -codProdSunat: null
      -codProdGS1: null
      -descripcion: "PROD 5"
      -mtoValorUnitario: 0.0
      -cargos: null
      -descuentos: null
      -descuento: null
      -mtoBaseIgv: 200.0
      -porcentajeIgv: 0.0
      -igv: 0.0
      -tipAfeIgv: "32"
      -mtoBaseIsc: null
      -porcentajeIsc: null
      -isc: null
      -tipSisIsc: null
      -mtoBaseOth: null
      -porcentajeOth: null
      -otroTributo: null
      -icbper: null
      -factorIcbper: null
      -totalImpuestos: 0.0
      -mtoPrecioUnitario: 0.0
      -mtoValorVenta: 200.0
      -mtoValorGratuito: null
      -atributos: null
    }
  ]
  #legends: array:2 [▼
    0 => 
Greenter\Model\Sale
\
Legend
 {#1575 ▼
      -code: "1000"
      -value: "QUINIENTOS TREINTA Y SEIS CON 00/100 SOLES"
    }
    1 => 
Greenter\Model\Sale
\
Legend
 {#1585 ▼
      -code: "1002"
      -value: "TRANSFERENCIA GRATUITA DE UN BIEN Y/O SERVICIO PRESTADO GRATUITAMENTE"
    }
  ]
  #guias: null
  #relDocs: null
  #compra: null
  #formaPago: 
Greenter\Model\Sale\FormaPagos
\
FormaPagoContado
 {#1573 ▼
    #moneda: null
    #tipo: "Contado"
    #monto: null
  }
  #cuotas: null
  -tipoOperacion: "0101"
  -fecVencimiento: null
  -sumDsctoGlobal: null
  -mtoDescuentos: null
  -sumOtrosDescuentos: null
  -descuentos: null
  -cargos: null
  -mtoCargos: null
  -totalAnticipos: null
  -perception: null
  -guiaEmbebida: null
  -anticipos: null
  -detraccion: null
  -seller: null
  -valorVenta: 500.0
  -subTotal: 536.0
  -observacion: null
  -direccionEntrega: null
}