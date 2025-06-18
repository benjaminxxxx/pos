<template>
  <div>
    <h3 class="text-lg font-medium text-center mb-4">Venta Procesada Exitosamente</h3>

    <div class="grid grid-cols-2 gap-3">
      <button
        type="button"
        @click="imprimirVoucher"
        class="flex items-center justify-center gap-2 h-14 border rounded-md px-4 hover:bg-gray-100"
      >
        <PrinterIcon class="h-5 w-5" />
        <div class="flex flex-col items-start">
          <span>Imprimir</span>
          <span class="text-xs text-muted-foreground">Voucher</span>
        </div>
      </button>

      <button
        type="button" 
         @click="imprimirFactura"
        class="flex items-center justify-center gap-2 h-14 border rounded-md px-4 hover:bg-gray-100"
      >
        <FileTextIcon class="h-5 w-5" />
        <div class="flex flex-col items-start">
          <span>Imprimir</span>
          <span class="text-xs text-muted-foreground">Factura A4</span>
        </div>
      </button>

      <!-- Enviar por correo -->
       <!--
      <button
        @click="emailDialogOpen = true"
        class="flex items-center justify-center gap-2 h-14 bg-slate-500 text-white rounded-md px-4 hover:bg-indigo-700"
      >
        <MailIcon class="h-5 w-5" />
        <div class="flex flex-col items-start">
          <span>Enviar por</span>
          <span class="text-xs">Correo</span>
        </div>
      </button>
       -->
      

      <!-- Descargar PDF -->
      <button 
       @click="descargarFactura"
        class="flex items-center justify-center gap-2 h-14 border rounded-md px-4 hover:bg-gray-100"
      >
        <DownloadIcon class="h-5 w-5" />
        <div class="flex flex-col items-start">
          <span>Descargar</span>
          <span class="text-xs text-muted-foreground">PDF</span>
        </div>
      </button>
    </div>

    <!-- Dialogo de correo -->
    <div v-if="emailDialogOpen" class="fixed inset-0 bg-black bg-opacity-30 z-50 flex items-center justify-center">
      <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <h4 class="text-lg font-semibold mb-4">Enviar comprobante por correo</h4>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1" for="email">Correo electrónico</label>
            <input
              id="email"
              v-model="email"
              type="email"
              placeholder="cliente@ejemplo.com"
              class="w-full border rounded px-3 py-2"
            />
          </div>
          <div class="flex justify-end gap-2">
            <button @click="emailDialogOpen = false" class="px-4 py-2 border rounded">Cancelar</button>
            <button
              @click="handleSendEmail"
              :disabled="sending || !email"
              class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700"
            >
              {{ sending ? "Enviando..." : "Enviar" }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, defineProps } from 'vue'
import { PrinterIcon, MailIcon, FileTextIcon, DownloadIcon } from 'lucide-vue-next'

const props = defineProps({
  ventaActiva: Object,
})

const emailDialogOpen = ref(false)
const email = ref('')
const sending = ref(false)
const abrirYImprimir = (url) => {
  if (!url) {
    alert("No hay documento disponible.")
    return
  }

  const iframe = document.createElement('iframe')
  iframe.style.display = 'none'
  iframe.src = url

  iframe.onload = () => {
    iframe.contentWindow.focus()
    iframe.contentWindow.print()
    //setTimeout(() => document.body.removeChild(iframe), 1000)
  }

  document.body.appendChild(iframe)
}
const imprimirVoucher = () => {
  abrirYImprimir(props.ventaActiva.voucher_pdf)
}

const imprimirFactura = () => {
  abrirYImprimir(props.ventaActiva.sunat_comprobante_pdf)
}

const descargarFactura = () => {
  const url = props.ventaActiva.sunat_comprobante_pdf
  if (!url) {
    alert("No hay factura A4 disponible.")
    return
  }

  const link = document.createElement('a')
  link.href = url
  link.download = '' // Puedes poner un nombre personalizado aquí
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}


const handleSendEmail = async () => {
  sending.value = true
  await new Promise(resolve => setTimeout(resolve, 1000)) // Simulación de API
  sending.value = false
  emailDialogOpen.value = false
  alert('Documento enviado correctamente')
}
</script>

