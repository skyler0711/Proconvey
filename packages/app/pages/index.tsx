import { UserRole } from 'gql/graphql'
import { useRouter } from 'next/router'
import { useSelector } from 'react-redux'
import { RootState } from 'store'

export default function Home () {
  const router = useRouter()

  const { auth } = useSelector((state: RootState) => ({
    auth: state.auth,
  }))

  if (auth.isLoading) {
    return null
  }

  if (!auth.user) {
    router.replace('/login')
  }

  if (auth.user?.role === UserRole.Conveyancer) {
    router.replace('/clients')
  }

  if (auth.user?.role === UserRole.Client) {
    router.replace('/properties')
  }

  return null
}
