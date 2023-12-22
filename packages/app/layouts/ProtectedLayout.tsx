import { UserRole } from 'gql/graphql'
import LoadingSpinner from '@proconvey/ui/src/components/LoadingSpinner'
import { useRouter } from 'next/router'
import { useSelector } from 'react-redux'
import { RootState } from 'store'

const Loading = () => {
  return (
    <div className="flex items-center justify-center w-full h-full">
      <LoadingSpinner />
    </div>
  )
}

const ProtectedLayout = ({ children }: { children: React.ReactNode }) => {
  const router = useRouter()

  const { isLoading, user } = useSelector((state: RootState) => ({
    isLoading: state.auth.isLoading,
    user: state.auth.user,
  }))

  if (!isLoading && !user) {
    router.push('/login')
    return <Loading />
  }

  if (isLoading) {
    return <Loading />
  }

  const isConveyancer = user && user.role === UserRole.Conveyancer

  if (isConveyancer &&
    (!user.job_bio || !user.job_role) && !user.first_name && !user.last_name &&
    !router.pathname.startsWith('/register/')
  ) {
    router.push('/register/profile')
    return <Loading />
  }

  if (isConveyancer && !user.first_name && !user.last_name &&
    !user.business_created_at && !user.conveyancer &&
    !router.pathname.startsWith('/register/')
  ) {
    router.push('/register/business')
    return <Loading />
  }

  return <>
    {children}
  </>
}

export default ProtectedLayout
