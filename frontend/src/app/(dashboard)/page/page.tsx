'use client'

import { useEffect } from 'react'

import { client } from '@/api/client'

export default function Page(){
  useEffect(() => {
    client.get('api/ping')
      .then(data => console.log('API Health:', data))
      .catch(err => console.error('API Error:', err))
  }, [])

  return <div><h1>NutriLedger</h1></div>

}
