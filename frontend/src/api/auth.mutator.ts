import { NextRequest, NextResponse } from 'next/server'
import { cookies } from 'next/headers'
import { options } from 'kolorist'


export const customFetchMutator = async <T>(url: string, options: RequestInit): Promise<T>  => {
  // 1. Get the token from Next.js server cookies
  const cookieStore = await cookies();
  const token = cookieStore.get('auth_token')?.value?.split('|')[1];

  // 2. headers setup
  const headers = new Headers(options.headers);

  headers.set('Accept', 'application/json');

  if (token){
    headers.set('Authorization', `Bearer ${token}`);
  }

  //3.  actual request
  const response = await fetch(url, {...options, headers});

  //4. handle errors
  if (!response.ok) {
    throw new Error(`API Error: ${response.statusText}`);
  }

  return response.json();
}
