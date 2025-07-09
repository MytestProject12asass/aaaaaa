import React from 'react'
import { Check, Star, ArrowRight } from 'lucide-react'
import { motion } from 'framer-motion'
import { formatCurrency } from '../lib/utils'

interface PricingPlan {
  id: string
  name: string
  description: string
  price: number
  period: string
  originalPrice?: number
  features: string[]
  popular?: boolean
  buttonText: string
  buttonVariant: 'primary' | 'secondary'
}

interface PricingCardProps {
  plan: PricingPlan
  className?: string
}

const PricingCard: React.FC<PricingCardProps> = ({ plan, className = '' }) => {
  const discount = plan.originalPrice 
    ? Math.round(((plan.originalPrice - plan.price) / plan.originalPrice) * 100)
    : 0

  return (
    <motion.div
      whileHover={{ y: -8, scale: 1.02 }}
      transition={{ duration: 0.3 }}
      className={`pricing-card ${plan.popular ? 'featured' : ''} ${className}`}
    >
      {/* Popular Badge */}
      {plan.popular && (
        <div className="absolute -top-4 left-1/2 transform -translate-x-1/2">
          <div className="bg-gradient-to-r from-primary-500 to-primary-600 text-white px-4 py-2 rounded-full text-sm font-medium flex items-center space-x-1">
            <Star className="h-4 w-4 fill-current" />
            <span>Most Popular</span>
          </div>
        </div>
      )}

      {/* Header */}
      <div className="text-center mb-8">
        <h3 className="text-2xl font-bold text-gray-900 mb-2">{plan.name}</h3>
        <p className="text-gray-600 mb-6">{plan.description}</p>
        
        {/* Pricing */}
        <div className="mb-4">
          {plan.originalPrice && (
            <div className="flex items-center justify-center space-x-2 mb-2">
              <span className="text-lg text-gray-400 line-through">
                {formatCurrency(plan.originalPrice)}
              </span>
              <span className="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium">
                Save {discount}%
              </span>
            </div>
          )}
          
          <div className="flex items-baseline justify-center">
            <span className="text-5xl font-bold text-gray-900">
              {formatCurrency(plan.price).split('.')[0]}
            </span>
            <span className="text-xl text-gray-500 ml-1">
              /{plan.period}
            </span>
          </div>
        </div>
      </div>

      {/* Features */}
      <div className="mb-8">
        <ul className="space-y-4">
          {plan.features.map((feature, index) => (
            <li key={index} className="flex items-start space-x-3">
              <div className="flex-shrink-0 w-5 h-5 bg-green-100 rounded-full flex items-center justify-center mt-0.5">
                <Check className="h-3 w-3 text-green-600" />
              </div>
              <span className="text-gray-700 leading-relaxed">{feature}</span>
            </li>
          ))}
        </ul>
      </div>

      {/* CTA Button */}
      <button
        className={`w-full group ${
          plan.buttonVariant === 'primary' ? 'btn-primary' : 'btn-secondary'
        }`}
      >
        {plan.buttonText}
        <ArrowRight className="ml-2 h-4 w-4 group-hover:translate-x-1 transition-transform duration-200" />
      </button>

      {/* Additional Info */}
      <div className="mt-6 text-center">
        <p className="text-sm text-gray-500">
          No setup fees • Cancel anytime • 30-day money-back guarantee
        </p>
      </div>
    </motion.div>
  )
}

export default PricingCard