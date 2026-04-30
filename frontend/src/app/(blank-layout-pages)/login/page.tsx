import type { Metadata } from 'next'
import Login from '@views/Login'
import { getServerMode } from '@core/utils/serverHelpers'

export const metadata: Metadata = {
  title: 'Login',
  description: 'Login to your account',
}

type Props = {
  searchParams: Promise<{ callbackUrl?: string }>
}

const LoginPage = async ({ searchParams }: Props) => {
  const { callbackUrl } = await searchParams
  const mode = await getServerMode()

  return <Login mode={mode} callbackUrl={callbackUrl} />
}

export default LoginPage
