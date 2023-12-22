import { ComponentStory, ComponentMeta } from '@storybook/react'
import IconButton from './index'
import { BellIcon } from '../../icons'

export default {
  title: 'Components/IconButton',
  component: IconButton,
} as ComponentMeta<typeof IconButton>

const Template: ComponentStory<typeof IconButton> = (args) => <IconButton {...args} />

export const Button: ComponentStory<typeof IconButton> = Template.bind({})
Button.args = {
  icon: <BellIcon />,
}

export const DisabledButton: ComponentStory<typeof IconButton> = Template.bind({})
DisabledButton.args = {
  icon: <BellIcon />,
  disabled: true,
}
