import { useEffect } from 'react'
import { useDispatch } from 'react-redux'
import { useQuery } from 'urql'
import { graphql } from 'gql'
import { loginViaSession } from 'slices/auth'
import LoadingSpinner from '@proconvey/ui/src/components/LoadingSpinner'
import Logo from '@proconvey/ui/src/svgs/logo'

const ApplicationLayout = ({ children }: { children: JSX.Element }) => {

  const dispatch = useDispatch()

  const [{ data, fetching }] = useQuery({
    query: graphql(`
      query me {
        me {
          id
          title
          first_name
          last_name
          suffix
          phone
          email
          sra_clc_number
          role
          conveyancer {
            id
            name
            sra_clc_number
            team_member_count
            type
          }
          unread_notifications {
            id
            type
            notifiable_type
            notifiable_id
            data {
              type
              id
              message
            }
            read_at
            created_at
          }
          job_role
          job_bio
          profile_image {
            id
            url
          }
        }
      }
    `),
  })

  useEffect(() => {
    if (data) {
      dispatch(loginViaSession(data.me!))
    }
  }, [data, dispatch])

  if (fetching) {
    return (
      <div className="flex flex-col items-center justify-center w-full h-screen gap-5 mx-auto my-auto">
        <Logo className="w-[250px] h-auto" />
        <LoadingSpinner />
      </div>
    )
  }

  return children
}

export default ApplicationLayout
