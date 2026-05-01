import type { NextConfig } from 'next'

const nextConfig: NextConfig = {
  basePath: process.env.BASEPATH,
  redirects: async () => {
    return [
      {
        source: '/',
        destination: '/dashboard/home',
        permanent: true,
        locale: false
      }
    ]
  }
}

export default nextConfig
