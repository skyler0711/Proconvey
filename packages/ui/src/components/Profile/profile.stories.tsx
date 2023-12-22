import { ComponentStory, ComponentMeta } from '@storybook/react'
import Profile from './index'

export default {
  title: 'Components/Profile',
  component: Profile,
} as ComponentMeta<typeof Profile>

const Template: ComponentStory<typeof Profile> = (args) => <Profile {...args} />

export const Account: ComponentStory<typeof Profile> = Template.bind({})
Account.args = {
  user: {
    first_name: 'Darlene',
    last_name: 'Robertson',
  },
  children: <>
    <Profile.Item>Account Settings</Profile.Item>
    <Profile.Item>Account Profile</Profile.Item>
    <Profile.Item>Logout</Profile.Item>
  </>,
}
