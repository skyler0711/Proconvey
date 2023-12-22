import { graphql } from 'gql'
import { useRouter } from 'next/router'
import { useDispatch, useSelector } from 'react-redux'
import { logout } from 'slices/auth'
import { RootState } from 'store'
import { useMutation } from 'urql'

const useLogout = () => {

  const router = useRouter()

  const dispatch = useDispatch()

  const { user } = useSelector((state: RootState) => ({
    user: state.auth.user!,
  }))

  const [_, logoutMutation] = useMutation(graphql(`
    mutation logout {
      logout
    }
  `))

  const handleLogout = () => {
    logoutMutation({}) // Don't need to await this
    dispatch(logout())
    router.push('/login')
  }

  return { user, handleLogout }
}

export default useLogout