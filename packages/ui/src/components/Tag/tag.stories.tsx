import { ComponentStory, ComponentMeta } from '@storybook/react'
import Tag from './index'

export default {
  title: 'Components/Tag',
  component: Tag,
} as ComponentMeta<typeof Tag>

const Template: ComponentStory<typeof Tag> = (args) => <Tag {...args} />

export const Default: ComponentStory<typeof Tag> = Template.bind({})
Default.args = {
  children: 'Default',
}

export const Success: ComponentStory<typeof Tag> = Template.bind({})
Success.args = {
  variant: 'success',
  children: 'Success',
}

export const Danger: ComponentStory<typeof Tag> = Template.bind({})
Danger.args = {
  variant: 'danger',
  children: 'Danger',
}
