import type { UserResource } from '@/api/generated/nutriBaseAPI.schemas'

export interface AuthUser {
  id: number
  name: string
  email: string
  role: 'admin' | 'doktor' | 'pacijent'
}

export interface AuthContextValue {
  user: AuthUser | null
  isLoading: boolean
  logout: () => Promise<void>
}

export function toAuthUser(resource: UserResource): AuthUser {
  return {
    id: resource.id,
    name: resource.attributes.name,
    email: resource.attributes.email,
    role: resource.attributes.role as AuthUser['role'],
  }
}
