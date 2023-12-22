import Button from '@proconvey/ui/src/components/Button'
import { H1 } from '@proconvey/ui/src/components/Headers'
import Logo from '@proconvey/ui/src/svgs/logo'
import { NextSeo } from 'next-seo'
import Link from 'next/link'
import { useSelector } from 'react-redux'
import { RootState } from 'store'
import { UserRole } from 'types/enums/UserRole'

export default function Custom404 () {
  const { user } = useSelector((state: RootState) => ({
    user: state.auth.user,
  }))

  return (
    <>
      <NextSeo
        title="Page Not Found"
      />
      <div className="flex flex-col items-center justify-center w-full h-screen gap-[120px]">
        <Logo className="w-[400px]" />
        <H1>Page Not Found</H1>

        <div className="flex items-center gap-10">
          <a href="https://proconvey.co.uk">
            <Button variant="link">Return to Homepage</Button>
          </a>

          {
            user && user.role === UserRole.Conveyancer
              ? (
                <Link href="/clients">
                  <Button size="small">Back to Clients</Button>
                </Link>
              )
              : user && user.role === UserRole.Client
                ? (
                  <Link href="/properties">
                    <Button size="small">Back to Properties</Button>
                  </Link>
                )
                : null
          }

        </div>
      </div>
    </>
  )
}
