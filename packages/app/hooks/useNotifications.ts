import { graphql } from 'gql'
import { useEffect } from 'react'
import { useDispatch } from 'react-redux'
import { updateAuthUser } from 'slices/auth'
import { useMutation } from 'urql'

const useNotifications = () => {
  const dispatch = useDispatch()

  const [{ data, fetching }, markAllNotificationsReadMutation] = useMutation(graphql(`
    mutation markAllNotificationsRead {
      markAllNotificationsRead {
        id
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
      }
    }
  `))

  const markAllNotificationsRead = () => {
    markAllNotificationsReadMutation({})
  }

  useEffect(() => {
    if (data?.markAllNotificationsRead) {
      dispatch(updateAuthUser(data.markAllNotificationsRead))
    }
  }, [data, dispatch])

  return { markAllNotificationsRead, fetching }
}

export default useNotifications
