import { createClient } from 'https://cdn.jsdelivr.net/npm/@supabase/supabase-js/+esm'

console.log('📡 Supabase realtime script dimulai')

const supabase = createClient(
  'https://realtime.baduo.cloud',
  'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyAgCiAgICAicm9sZSI6ICJhbm9uIiwKICAgICJpc3MiOiAic3VwYWJhc2UtZGVtbyIsCiAgICAiaWF0IjogMTY0MTc2OTIwMCwKICAgICJleHAiOiAxNzk5NTM1NjAwCn0.dc_X5iR_VP_qT0zsiyj_I_OZ2T9FtRU2BBNWN8Bu4GE'
)
const channel = supabase.channel('realtime_iclock_transaction')
channel.on(
  'postgres_changes',
  {
    event: 'INSERT',
    schema: 'public',
    table: 'iclock_transaction',
  },
  (payload) => {
    console.log('🔥 Realtime payload:', payload)

    const data = payload.new
    console.log('📥 Data baru:', data)

    if (typeof Livewire !== 'undefined') {
      Livewire.dispatch('fingerprint-updated', {
        mesin: data.terminal_sn,
        data: data,
      })
    } else {
      console.warn('⚠️ Livewire tidak tersedia di window')
    }
  }
)
channel
  .on(
    'postgres_changes',
    {
      event: 'DELETE',
      schema: 'public',
      table: 'iclock_transaction',
    },
    (payload) => {
      console.log('🗑️ Data DELETE:', payload)
      const data = payload.old
      console.log('📤 Data lama:', data)
      if (typeof Livewire !== 'undefined') {
        Livewire.dispatch('fingerprint-deleted', {
          mesin: payload.old,
          data: payload.old,
        })
      }
    }
  )

  .subscribe((status) => {
    console.log('📶 Channel status:', status)
  })
