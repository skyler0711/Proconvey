import { ComponentStory, ComponentMeta } from '@storybook/react'
import Searchbar from './index'

export default {
  title: 'Components/Searchbar',
  component: Searchbar,
} as ComponentMeta<typeof Searchbar>

const Template: ComponentStory<typeof Searchbar> = (args) => <Searchbar {...args} />

export const WithSuggestions: ComponentStory<typeof Searchbar> = Template.bind({})
