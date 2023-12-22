import { ComponentStory, ComponentMeta } from '@storybook/react'
import { SettingsIcon, UserIcon } from '../../icons'
import Menu from './index'

export default {
  title: 'Components/Menu',
  component: Menu,
} as ComponentMeta<typeof Menu>

const Template: ComponentStory<typeof Menu> = (args) => {
  return (
    <Menu {...args}>
      <Menu.Item icon={<UserIcon />} to="#">
        Clients
      </Menu.Item>

      <Menu.Dropdown icon={<SettingsIcon />} text="Settings" defaultOpen={true}>
        <Menu.SubItem to="#" active={true}>Overview</Menu.SubItem>
        <Menu.SubItem to="#">Profile Settings</Menu.SubItem>
        <Menu.SubItem to="#">Business Settings</Menu.SubItem>
        <Menu.SubItem to="#">Billing</Menu.SubItem>
        <Menu.SubItem to="#">Notification Settings</Menu.SubItem>
      </Menu.Dropdown>
    </Menu>
  )
}


export const Account: ComponentStory<typeof Menu> = Template.bind({})
