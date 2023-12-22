import { ComponentStory, ComponentMeta } from '@storybook/react'
import NotificationsDropdown from './index'
import dayjs from 'dayjs'

export default {
  title: 'Components/NotificationsDropdown',
  component: NotificationsDropdown,
} as ComponentMeta<typeof NotificationsDropdown>

const data = {
  type: 'profile_settings',
  id: null,
  message: 'There are items that require your attention',
}

export const PendingNotifications: ComponentStory<typeof NotificationsDropdown> = () => {
  return (
    <NotificationsDropdown length={6} markAllNotificationsRead={() => {}}>
      <NotificationsDropdown.Item notificationData={data} timestamp={dayjs().subtract(1, 'hour').toString()}>
        There are items that require your attention
      </NotificationsDropdown.Item>
      <NotificationsDropdown.Item notificationData={data} timestamp={dayjs().subtract(1, 'hour').toString()}>
        There are items that require your attention
      </NotificationsDropdown.Item>
      <NotificationsDropdown.Item notificationData={data} timestamp={dayjs().subtract(1, 'hour').toString()}>
        There are items that require your attention
      </NotificationsDropdown.Item>
      <NotificationsDropdown.Item notificationData={data} timestamp={dayjs().subtract(1, 'hour').toString()}>
        There are items that require your attention
      </NotificationsDropdown.Item>
      <NotificationsDropdown.Item notificationData={data} timestamp={dayjs().subtract(1, 'hour').toString()}>
        There are items that require your attention
      </NotificationsDropdown.Item>
      <NotificationsDropdown.Item notificationData={data} timestamp={dayjs().subtract(1, 'hour').toString()}>
        There are items that require your attention
      </NotificationsDropdown.Item>
    </NotificationsDropdown>
  )
}

export const EmptyNotifications: ComponentStory<typeof NotificationsDropdown> = () => {
  return (
    <NotificationsDropdown length={0}>
    </NotificationsDropdown>
  )
}
