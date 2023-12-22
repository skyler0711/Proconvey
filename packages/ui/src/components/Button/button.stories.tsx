import { ComponentStory, ComponentMeta } from '@storybook/react'
import Button from './index'

export default {
  title: 'Components/Button',
  component: Button,
} as ComponentMeta<typeof Button>

const Template: ComponentStory<typeof Button> = (args) => <Button {...args} />

export const Primary: ComponentStory<typeof Button> = Template.bind({})
Primary.args = {
  children: 'Primary',
}

export const Secondary: ComponentStory<typeof Button> = Template.bind({})
Secondary.args = {
  variant: 'secondary',
  children: 'Secondary',
}

export const Tertiary: ComponentStory<typeof Button> = Template.bind({})
Tertiary.args = {
  variant: 'tertiary',
  children: 'Tertiary',
}

export const Outlined: ComponentStory<typeof Button> = Template.bind({})
Outlined.args = {
  variant: 'outlined',
  children: 'Outlined',
}

export const Link: ComponentStory<typeof Button> = Template.bind({})
Link.args = {
  variant: 'link',
  children: 'Text Link',
}
