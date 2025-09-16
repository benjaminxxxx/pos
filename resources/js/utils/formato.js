export function formatoSoles(valor) {
  if (valor == null || isNaN(valor)) return "S/. 0.00"
  return "S/. " + Number(valor).toLocaleString("es-PE", {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  })
}