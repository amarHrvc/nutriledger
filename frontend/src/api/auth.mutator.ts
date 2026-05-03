import { cookies } from 'next/headers'

export const customFetchMutator = async <T>(url: string, options: RequestInit): Promise<{ data: T; status: number }> => {
  const cookieStore = await cookies();
  const token = cookieStore.get('auth_token')?.value?.split('|')[1];

  const headers = new Headers(options.headers);
  headers.set('Accept', 'application/json');
  if (token) {
    headers.set('Authorization', `Bearer ${token}`);
  }

  const response = await fetch(url, { ...options, headers });
  const data = response.status === 204 ? null : await response.json();

  return { data, status: response.status };
}
